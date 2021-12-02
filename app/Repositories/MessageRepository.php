<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Message;

/**
 * Class MessageRepository.
 */
class MessageRepository 
{
    public function __construct(Message $model)
    {
        $this->model = $model;
    }

    /**
     * Obtém as mensagens do cliente ou do advogado
     *
     */
    public function getMessages($advocateUserId = null, $userId = null)
    {
        //Obtém as mensagens do cliente
        if($userId){
            return $this->model->where('user_id', $userId)
                ->where('read',0)
                    ->orderBy('read','desc')->get();
        }   

        //Obtém as mensagens do advogado
        $clients = Client::where('advocate_user_id', $advocateUserId)->get();
        $clientIds = $clients->pluck('id')->toArray();
        
        $clientsUser = ClientUser::whereIn('client_id', $clientIds)->get();
        $usersIds = $clientsUser->pluck('user_id')->toArray();

        return $this->model->whereIn('user_id', $usersIds)
            ->where('read',0)
                ->orderBy('read', 'desc')->get();
    }

    /**
     * Cria as mensagens no banco de dados
     */
    public function store(Array $inputs)
    {
        return $this->model->create($inputs);
    }

    /**
     * Obtém as mensagens enviadas
     */
    public function getMessagesSent($userId, $clientSent)
    {
        if($clientSent){
            return $this->model->where('user_id', $userId)
                ->where('client_sent', true)->get();
        }

        return $this->model->where('user_id', $userId)
            ->where('client_sent', false);
        
    }
}
