<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\MessageReceived;

class MessageReceivedRepository 
{
    public function __construct(MessageReceived $model)
	{
		$this->model = $model;
	}

	public function getMessages(Int $advocateUserId)
	{
		
		$messagesReceived = [];
		
		$allMessages = $this->model->all();
		$messagesByAdvocate = $allMessages->where('advocate_user_id', 74);
		$clients = $messagesByAdvocate->groupBy('client_id');

		foreach($clients as $key => $client) {	

			$messagesByClient = [];
			$findClient = Client::find($key);
			
			foreach($client as $value){
				array_push($messagesByClient, $value);
			}

			$findClient->messages = $messagesByClient;
			array_push($messagesReceived, $findClient);
		}

		return $messagesReceived;
	}

	/**
	 * Salva as mensagens enviadas pelo cliente
	 */
	public function store(Array $inputs)
	{
		$clientId = $inputs['client_id'];
        $count = $this->model->where('client_id', $clientId)->count();
        $inputs['code_message'] = $count + 1;
        
        return $this->model->create($inputs);
	}	
}
