<?php

namespace App\Repositories;

use App\Models\Advocate;
use App\Models\Client;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF;
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
        $contract = $this->model->create($inputs);

        if($contract) 
        {
            $url = $this->generateContract($contract);
            $contract = $this->model->where('id', $contract->id)->update(["link_contract" => $url]);
        }

        return $contract;
    } 

    /**
     * Atualiza um contrato
     */
    public function update(Contract $contract, Array $inputs)
    {
        $contract->update($inputs);
        $this->generateContract($contract);
        
        return $contract;
    }

    /**
     * Geração do contrato
     * @param Contract $contract
     */
    public function generateContract(Contract $contract)
    {
        $client = Client::find($contract->client_id);
        $contract->client = $client;

        $advocate = Advocate::find($contract->advocate_id);
        $contract->advocate = $advocate;

        $contract->day = Carbon::parse($contract->start_date)->locale('pt-BR')->format('d');
        $contract->month = Carbon::parse($contract->start_date)->locale('pt-BR')->translatedFormat('F');
        $contract->year = Carbon::parse($contract->start_date)->format('Y');


        $startDate = Carbon::parse($contract->start_date);
        $finishDate = Carbon::parse($contract->finish_date);
        $diff = $startDate->diffInMonths($finishDate);
        $contract->contract_days = $diff;

        $pdf = PDF::loadView('contract', [ 
                'contract'  => $contract
            ]
        );

        $fileName = 'contrato.pdf';
        $path = 'contracts/'.$contract->id.'/'.$fileName;

        Storage::disk('s3')->deleteDirectory($path);
        Storage::disk('s3')->put($path, $pdf->output());

        return Storage::disk('s3')->url($path);
    }
}
