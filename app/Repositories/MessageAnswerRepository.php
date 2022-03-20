<?php

namespace App\Repositories;

use App\Models\MessageAnswer;

/**
 * Class MessageAnswerRepository.
 */
class MessageAnswerRepository
{
    public function __construct(MessageAnswer $model)
	{
		$this->model = $model;
	}

    /**
	 * Salva as respostas enviadas
	 */
	public function store(Array $inputs)
	{
        return $this->model->create($inputs);
	}	

}
