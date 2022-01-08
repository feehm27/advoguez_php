<?php

namespace App\Repositories;

use App\Models\Advocate;
use App\Models\Client;
use App\Models\Contract;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class ContractRepository.
 */
class ContractRepository
{
    public function __construct(Contract $model)
	{
		$this->model = $model;
	}

    /**
     * Obtém os contratos do advogado
     */
    public function getContracts(Int $advocateUserId)
    {
        $contracts = $this->model->where('advocate_user_id', $advocateUserId)->get();

        foreach($contracts as $contract) 
        {
            $client = Client::find($contract->client_id);
            $contract->client = $client;

            $advocate = Advocate::find($contract->advocate_id);
            $contract->advocate = $advocate;
        }

        return $contracts;
    }

    /**
     * Cria um contrato
     */
    public function create(Array $inputs) 
    {
        return $this->model->create($inputs);
    } 

    /**
     * Atualiza um contrato
     */
    public function update(Contract $contract, Array $inputs)
    {
        return $this->model->where('id', $contract->id)->update($inputs);
    }
}
