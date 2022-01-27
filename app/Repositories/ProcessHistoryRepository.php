<?php

namespace App\Repositories;

use App\Models\ProcessHistory;

/**
 * Class ProcessHistoryRepository.
 */
class ProcessHistoryRepository 
{
    public function __construct(ProcessHistory $model)
	{
		$this->model = $model;
	}

    /**
     * Obtém os historicos de um processo    
    */
    public function getProcessesHistory(Int $processId)
    {
        $processesHistory = $this->model->where('process_id', $processId)
            ->orderBy('modification_date', 'desc')
                ->get();

        return $processesHistory;
    }

    /**
     * Cria um historico do processo
     */
    public function create(Array $inputs) 
    {
        return $this->model->create($inputs);
    } 
}
