<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository 
{
    public function __construct(User $model)
	{
		$this->model = $model;
	}

    /**
     * Obtém os usuários vinculados ao advogado
     */
    public function getUsers(User $advocateUser)
    {
        $allUsers = new Collection();
        $clients = Client::where('advocate_user_id', $advocateUser->id)->get();

        if(!$clients->isEmpty()) {

            $clientsIds = $clients->pluck('id');
            $clientUsers = ClientUser::whereIn('client_id', $clientsIds)->get();
        
            if(!$clientUsers->isEmpty()){
                
                $usersIds = $clientUsers->pluck('user_id');
                $users = $this->model->whereIn('id', $usersIds)->with('clientUser')->get();

                if(!$users->isEmpty()){
                    $allUsers = $users;
                }
            }
        }

        $usersByAdvocate = $this->model->where('advocate_user_id', $advocateUser->id)
            ->with('clientUser')->get();

        $allUsers = $allUsers->merge($usersByAdvocate)->push($advocateUser);
    
        return $allUsers;
    }

    /**
     * Atualiza um usuário
    */
    public function update(Array $inputs)
    {
        if(isset($inputs['client_id'])){
           
            $clientId =  $inputs['client_id'];
            $userId = $inputs['id'];

            $clientUser = ClientUser::where('client_id', $clientId)
                ->where('user_id', $userId)
                ->first();

            if(!$clientUser) {
                ClientUser::create(['client_id' => $inputs['client_id'], 'user_id' => $inputs['id']]);
            }

            unset($inputs['client_id']);
        }

		return $this->model->where('id', $inputs['id'])->update($inputs);
    }

    /**
     * Bloqueia um usuário
     */
    public function lockOrUnlock(User $user, $input)
    {
        return $this->model->where('id', $user->id)->update($input);
    } 

    /**
     * Deleta um usuário
     */
    public function delete(User $user)
    {


        return $user->delete();
    }
}
