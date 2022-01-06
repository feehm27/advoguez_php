<?php

namespace App\Repositories;

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
    public function getContracts(Int $advocateId)
    {
        return $this->model->where('advocate_id', $advocateId)->get();
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
		$contract->update($inputs);
        return $contract;
    }

    /**
     * Cancela um contrato
     */
    public function cancelContract(Contract $contract, $canceledAt)
    {
        $contract->update(["canceled_at" => $canceledAt]);
        return $contract;
    }

}
