<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\MessageAnswer;
use App\Models\MessageReceived;
use App\Models\User;

class MessageReceivedRepository 
{
    public function __construct(MessageReceived $model)
	{
		$this->model = $model;
	}

	/**
	 * Obtém as mensagens
	 */
	public function getMessages(Int $advocateUserId)
	{
		
		$messagesReceived = [];
		
		$allMessages = $this->model->orderBy('created_at', 'desc')->get();
		$messagesByAdvocate = $allMessages->where('advocate_user_id', $advocateUserId);
		$clients = $messagesByAdvocate->groupBy('client_id');

		foreach($clients as $key => $client) {	

			$messagesByClient = [];
			$findClient = Client::find($key);
			
			foreach($client as $value) {
				$answers = MessageAnswer::where('message_received_id', $value->id)->orderBy('created_at', 'desc')->get();
				$value->answers = $answers;
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

	/**
	 * Deleta todas as mensagens
	 */
	public function deleteAllMessages(Int $clientId)
	{
		$messagesReceivedIds = $this->model->where('client_id', $clientId)->pluck('id')->toArray();
		MessageAnswer::whereIn('message_received_id', $messagesReceivedIds)->delete();
		$this->model->whereIn('id', $messagesReceivedIds)->delete();
	}

	/**
	 * Deleta uma mensagem e seus vinculos
	 */
	public function deleteMessage(MessageReceived $messageReceived)
	{
		MessageAnswer::where('message_received_id', $messageReceived->id)->delete();
		$messageReceived->delete();
	}

	public function getMessagesByClient(Int $clientId)
	{
		$messagesClient = $this->model->where('client_id', $clientId)->orderBy('created_at', 'desc')->get();

		foreach($messagesClient as $message) {

			$answers = MessageAnswer::where('message_received_id', $message->id)
				->orderBy('created_at', 'desc')->get();

			$advocate = User::find($message->advocate_user_id);

			$message->answers = $answers;
			$message->advocate = $advocate;
		}

		return $messagesClient;
	}
}
