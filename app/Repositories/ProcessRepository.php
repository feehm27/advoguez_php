<?php

namespace App\Repositories;

use App\Models\ClientUser;
use App\Models\Process;
use App\Models\ProcessHistoric;
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

            $process->historics = $process->historics()
                ->orderBy('modification_date', 'desc')->get();
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
        $inputs['file'] = '';

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

    public function deleteProcessAndHistorics($process)
    {
        $historics = $process->historics()->get();

        if(!$historics->isEmpty())
        {
            $historicsIds = $historics->pluck('id')->toArray();
            ProcessHistoric::whereIn('id', $historicsIds)->delete();
        }

		return $process->delete();
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

    /**
     * Obtém o processo do cliente
     */
    public function getProcessByClient(Int $userId)
    {
        $status = null;
        $lastModification = null;
        $historics = []; 
        $numberProcess = null;
        $observation = 'Sem observações';

        $clientId = ClientUser::where('user_id', $userId)->first()->client_id;
        $process = $this->model->where('client_id', $clientId)->first();

        if($process) {

            $historics = $process->historics()->orderBy('modification_date', 'desc')->get();

            if(!$historics->isEmpty()) {

                $historic = $historics->first();
                $status = $historic->status_process;
                $lastModification = $historic->modification_date;

            }else{
                $status = $process->status;
                $lastModification = $process->start_date;
            }

            $numberProcess = $process->number;
            
            if($process->observation){
                $observation = $process->observation;
            }
        }

        return [
            'status'             => $status,
            'last_modification'  => $lastModification,
            'historics'          => $historics,
            'number_process'     => $numberProcess,
            'observation'        => $observation
        ];
    } 
}
