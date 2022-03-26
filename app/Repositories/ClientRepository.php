<?php

namespace App\Repositories;

use App\Http\Utils\HeaderPDFUtils;
use App\Http\Utils\MaskUtils;
use App\Mail\CanceledMettingMail;
use App\Models\AdvocateSchedule;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Contract;
use App\Models\MenuPermission;
use App\Models\Message;
use App\Models\User;

use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
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

    /**
     * Obtém os horários disponíveis para agendamento
     */
    public function getSchedules(Int $clientId, $date)
    {
        $currentDay = Carbon::now()->subDay()->format('Y-m-d');
        $endMonth = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $advocateUserId = $this->model->find($clientId)->advocate_user_id;

        $days = AdvocateSchedule::where('advocate_user_id', $advocateUserId)
            ->whereBetween('date', [$currentDay, $endMonth])
            ->where('time_type', 1)->get()->groupBy('date');
            
        return $days;
    }

    /**
     * Verifica se o cliente possui agendamento
     * @param Int $clientId
     */
    public function checkSchedule(Int $clientId)
    {
        $currentDate = Carbon::now()->subDay()->format('Y-m-d');

        $schedule = AdvocateSchedule::where('client_id', $clientId)
            ->whereDate('date' ,'>=', $currentDate)->first();

        if($schedule) {
            
            $advocate = $schedule->advocate()->first();
            $date = Carbon::parse($schedule->date)->format('d/m/Y');
            $hours = json_decode($schedule->horarys)->hours[0];

            $schedule->advocate = $advocate;
            $schedule->new_date = $date;
            $schedule->hours = $hours;
        }
        return $schedule;
    }

    /**
     * Cancela a reunião do cliente
     */
    public function cancelMeeting(Array $inputs)
    {
        $advocateUserId  = $inputs['advocate_user_id'];
        $clientId        = $inputs['client_id'];
        $date            = $inputs['date'];
        $horarysToAdd    = $inputs['horarys']['hours'];
        $email           = $inputs['email'];

        $scheduledTime = AdvocateSchedule::where('client_id', $clientId)
            ->where('date', $date)->where('time_type', 3)->first();

        $schedulesAdvocate = AdvocateSchedule::where('advocate_user_id', $advocateUserId)
            ->where('date', $date)->where('time_type', 1)->first();

        if($scheduledTime) {

            $horarysToAdd = json_decode($scheduledTime->horarys, true)['hours'];
            $scheduledTime->delete();

            if($schedulesAdvocate)
            {
                $horarysToShedule = json_decode($schedulesAdvocate->horarys, true)['hours'];
                array_push($horarysToShedule, $horarysToAdd[0]);           
                $object = array_values($horarysToShedule);
                $schedulesAdvocate->horarys = json_encode(["hours" => $object]);
                $schedulesAdvocate->save();

            }else {
                $inputs['horarys'] = json_encode($inputs['horarys']);
                $inputs['time_type'] = 1;
                $inputs['client_id'] = null;
                $inputs['color'] = 'white';
                AdvocateSchedule::create($inputs);
            }
    
            $this->sendMail($clientId, $inputs['advocate_name'], $email, $horarysToAdd[0], $date);
        }       
    }

    /**
     * Envia o email de cancelamento da reunião para o advogado
     */
    private function sendMail($clientId, $advocateName, $email, $hour, $date) 
    {
        $client = Client::find($clientId);

        $data = [
            "client_name"    => $client->name,
            "advocate_name"  => $advocateName,
            "hour"           => $hour,
            "date"           => Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y'),
            "is_advocate"    => true
        ];

        Mail::to($email)->send(new CanceledMettingMail($data));
    }
}
