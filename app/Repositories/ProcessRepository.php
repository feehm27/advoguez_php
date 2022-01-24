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
        $file = $inputs['file'];

        if($file) {
           $link = $this->uploadProcess($file, $process->client_id, $process->id);
           $inputs['file'] = $link;

        }else {
            unset($inputs['file']);
        }

        $process->update($inputs);

        return $process;
    }


    /**
     * Faz upload do processo
     */
    private function uploadProcess($file, $clientId, $processId) 
    {
        $path = $clientId.'/processes/'.$processId;
        Storage::disk('s3')->deleteDirectory($path);

        $upload = Storage::disk('s3')->put($path, $file);
        $urlPublic = Storage::disk('s3')->url($upload);

        return $urlPublic;
    }
}
