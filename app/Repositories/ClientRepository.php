<?php

namespace App\Repositories;

use App\Http\Utils\HeaderPDFUtils;
use App\Http\Utils\MaskUtils;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Contract;
use App\Models\MenuPermission;
use App\Models\Message;
use App\Models\User;

use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use PDF;


/**
 * Class ClientRepository.
 */
class ClientRepository 
{
    public function __construct(Client $model, AuthRepository $auth)
	{
		$this->model = $model;
        $this->auth = $auth;
	}

    /**
     * Obtém os clientes
     */
    public function getClients(Int $advocateUserId, $checkContract = null, $checkProcess = null)
    {
        $clients = $this->model->where('advocate_user_id', $advocateUserId)->get();

        if(!$clients->isEmpty()) {

            if($checkContract) {
                $clientsWithoutContract = $clients->reject(function ($client, $key) {
                    return $client->contract()->first();               
                });
                return $clientsWithoutContract->values();
            }

            if($checkProcess) {
                $clientsWithoutProcess = $clients->reject(function ($client, $key) {
                    return $client->process()->first();               
                });
                return $clientsWithoutProcess->values();
            }
        }
        
        return $clients;
    }

    /**
     * Cria o cliente e vincula o mesmo ao usuário
     */
    public function createClientAndUser(Array $inputsClient, Array $inputsUser)
    {
		$client = $this->model->create($inputsClient);
        $user = User::create($inputsUser);

        if($user){ 
            $user->createToken('auth_token')->plainTextToken;
            $this->auth->attachPermissions($user, false);
        }

        $clientUser = [
            'client_id' => $client->id,
            'user_id'   => $user->id
        ];

        ClientUser::create($clientUser);

        return $client;
    }

    /**
     * Atualiza um cliente
     */
    public function updateClient(Array $inputs)
    {
        $id = $inputs['id'];
		return $this->model->where('id', $id)->update($inputs);
    }

    /**
     * Deleta um cliente e seus vinculos
     */
    public function delete(Client $client)
    {
        $customerUser = $client->user()->first();

        if($customerUser) 
        {
            $user = User::find($customerUser->user_id);
            $permissions = $user->permissions()->get();

            $permissionsIds = $permissions->pluck('id');
            MenuPermission::whereIn('id', $permissionsIds)->delete();
            
            $customerUser->delete();
            $user->delete();
        }

        $clientUsers = ClientUser::where('client_id', $client->id)->get();

        if(!$clientUsers->isEmpty()) {

            $usersIds = $clientUsers->pluck('user_id')->toArray();
            
            $clientUsers->each(function ($clientUser) {
                $clientUser->delete();
            });

            $users = User::whereIn('id', $usersIds)->get();
            MenuPermission::whereIn('user_id', $usersIds)->delete();
            Message::whereIn('user_id', $usersIds)->delete();

            $users->each(function ($user) {
                $user->delete();
            });
        }

        $contracts = Contract::where('client_id', $client->id)->get();
        
        if(!$contracts->isEmpty()) {
            $contractIds = $contracts->pluck('id')->toArray();
            Contract::whereIn('id', $contractIds)->delete();
        }
        
        $client->delete();

    }

    /**
     * Gera o PDF de um cliente especifico ou de todos os clientes associados ao advogado
     */
    public function generatePDF(Client $client = null, $allClients, $clients = null, User $user)
    {   
        $title = 'Relatório de Clientes';
        $headers = HeaderPDFUtils::HEADER_CLIENTS;
        $logo = User::find($user->id)->logo;

        if(!$logo){
            $logo = env('DEFAULT_LOGO');
        }

        if($allClients){
            
            foreach($clients as $client)
            {
                $client->birthday = Carbon::parse($client->birthday)->format('d/m/Y');
                $client->telephone = MaskUtils::maskPhone($client->telephone);
                $client->cellphone = MaskUtils::maskPhone($client->cellphone);
                $client->cpf = MaskUtils::maskCPF($client->cpf);    
            }
            
            $body = $clients->toArray();
        
        }else {

            $client->birthday = Carbon::parse($client->birthday)->format('d/m/Y');
            $client->telephone = MaskUtils::maskPhone($client->telephone);
            $client->cellphone = MaskUtils::maskPhone($client->cellphone);
            $client->cpf = MaskUtils::maskCPF($client->cpf);

            $body = [$client->only(HeaderPDFUtils::ATTRIBUTES_CLIENT)];
        }

        $currentDate = Carbon::now()->subHours(3)->format('d/m/Y H:i:s');
        
        $pdf = PDF::loadView('generate-pdf', [ 
                'title'     => $title, 
                'logo'      => $logo,
                'headers'   => $headers,
                'date'      => $currentDate, 
                'rows'      => $body
            ]
        );

        $pdf->setPaper('letter', 'landscape');
    
        // Faz upload do arquivo no s3
        $fileName = $allClients ? 'clients' : 'client';
        $path = 'downloads/'.$user->id.'/'.$fileName;

        Storage::disk('s3')->deleteDirectory($path);
        Storage::disk('s3')->put($path, $pdf->output());

        return Storage::disk('s3')->url($path);
    }
}
