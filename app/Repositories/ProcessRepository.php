<?php

namespace App\Repositories;

use App\Models\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProcessRepository.
 */
class ProcessRepository
{
    public function __construct(Process $model)
	{
		$this->model = $model;
	}

    /**
     * Obtém os processos do advogado
     */
    public function getProcesses(Int $advocateUserId)
    {
        $processes = $this->model->where('advocate_user_id', $advocateUserId)
            ->orderBy('created_at', 'desc')
                ->get();

        foreach($processes as $process) {
            $process->client = $process->client()->first();
        }

        return $processes;
    }

    /**
     * Cria um processo
     */
    public function create(Array $inputs) 
    {
        $file = $inputs['file'];
        $clientId = $inputs['client_id'];
        unset($inputs['file']);

        $process = $this->model->create($inputs);
        $link = $this->uploadProcess($file, $clientId, $process->id);
        
        $process->file = $link;
        $process->save();

        return $process;
    } 

     /**
     * Atualiza um processo
     */
    public function update(Process $process, Array $inputs)
    {
        if(isset($inputs['file'])){
           $link = $this->uploadProcess($inputs['file'], $process->client_id, $process->id);
           $inputs['file'] = $link;
        }

        $process->update($inputs);

        return $process;
    }


    /**
     * Faz upload do processo
     */
    private function uploadProcess($file, $clientId, $processId) 
    {
        $path = 'processes/'.$processId;
        Storage::disk('s3')->deleteDirectory($path);
        
        $upload = Storage::disk('s3')->put($path, $file);
        $urlPublic = Storage::disk('s3')->url($upload);

        return $urlPublic;
    }
}
