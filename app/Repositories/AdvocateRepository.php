<?php

namespace App\Repositories;

use App\Models\Advocate;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class AdvocateRepository.
 */
class AdvocateRepository
{
	public function __construct(Advocate $model)
	{
		$this->model = $model;
	}

	/**
	 * Obtém as informações do advogado
	 */
	public function getInformationsByAdvocate(Int $userId)
	{
		$informations = $this->model->where('user_id', $userId)->first();
		return $informations;
	}

	/**
	 * Cria ou atualiza os dados do advogado
	 */
	public function storeOrUpdate(array $inputs)
	{
		$advocateId = $inputs['id'];

		if (isset($advocateId)) {
			return $this->model->where('id', $advocateId)->update($inputs);
		}

		return $this->model->create($inputs);
	}
}
