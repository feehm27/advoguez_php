<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\MenuPermission;
use App\Models\User;

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
    public function getClients(Int $advocateUserId)
    {
        return $this->model->where('advocate_user_id', $advocateUserId)->get();
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

        $client->delete();
    }
}
