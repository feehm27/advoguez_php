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
                    ->where('advocate_sent', 1)
                        ->orderBy('read','desc')->get();
        }   

        //Obtém as mensagens do advogado
        $clients = Client::where('advocate_user_id', $advocateUserId)->get();

        foreach($clients as $client) {

            $clientUsers = ClientUser::where('client_id', $client->id)->get();
            $usersIds = $clientUsers->pluck('user_id')->toArray();
            $messages = $this->model->whereIn('user_id', $usersIds)
                ->where('read',0)
                    ->orderBy('created_at', 'desc')->get();

            $client->messages = $messages;
        } 

       return $clients;
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
