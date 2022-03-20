<?php

namespace App\Repositories;

use App\Http\Utils\HeaderPDFUtils;
use App\Http\Utils\MaskUtils;
use App\Http\Utils\StatusCodeUtils;
use App\Models\Client;
use App\Models\ClientReport;
use App\Models\Contract;
use App\Models\ContractReport;
use App\Models\Process;
use App\Models\ProcessReport;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF;

/**
 * Class ReportRepository.
 */
class ReportRepository {

    public function __construct(Report $model, ClientReport $client, ContractReport $contract, ProcessReport $process)
	{
		$this->model = $model;
        $this->client = $client;
        $this->contract = $contract;
        $this->process = $process;
	}

    /**
     * Obtém os relatórios do advogado
     */
    public function getReports($advocateUserId) 
    { 
        $reports = $this->model->where('advocate_user_id', $advocateUserId)->get();

        if(!$reports->isEmpty()) {

            foreach($reports as $report) {
                if($report->type === 'Clientes')   $report->filters = $report->clientReport()->first();
                if($report->type === 'Contratos')  $report->filters = $report->contractReport()->first();
                if($report->type === 'Processos')  $report->filters = $report->processReport()->first();
            }
        }

        return $reports;
    }

    /**
     * Cria um novo relatório
     */
    public function create(Array $inputs) 
    {
        return $this->model->create($inputs);
    }

    /**
     * Cria um relatório de cliente, contrato ou processo e exporta para a amazon
     */
    public function createAndExport(String $type, Array $inputs, Report $report,  Int $advocateUserId, $typeReport = null) 
    {
        switch ($type) {

            case 'Clientes':    

                if($typeReport) {
                    $typeReport->update($inputs);
                    $clientReport = $this->client->find($typeReport->id);
                }else{
                    $clientReport = $this->client->create($inputs);
                }
               
                unset($inputs['report_id']);
                $filters = (array_filter($inputs));
                $clients = $this->filterClients($filters, $advocateUserId);
                $publicUrl = $this->generateReportClient($clients, $report);
                
                $clientReport->link_report = $publicUrl;
                $clientReport->save();

                return $clientReport;

            break;

            case 'Contratos':

                if($typeReport) {
                    $typeReport->update($inputs);
                    $contractReport = $this->contract->find($typeReport->id);
                }else{
                    $contractReport = $this->contract->create($inputs);
                }

                unset($inputs['report_id']);
                $filters = (array_filter($inputs));

                $contracts = $this->filterContracts($filters, $advocateUserId);
                $publicUrl = $this->generateReportContract($contracts, $report);

                $contractReport->link_report = $publicUrl;
                $contractReport->save();

                return $contractReport;

            break;

            case 'Processos':

                if($typeReport){
                    $typeReport->update($inputs);
                    $processReport = $this->process->find($typeReport->id);
                }else{
                    $processReport = $this->process->create($inputs);
                }

                unset($inputs['report_id']);
                $filters = (array_filter($inputs));

                $processes = $this->filterProcesses($filters, $advocateUserId);   
                $publicUrl = $this->generateReportProcess($processes, $report);

                $processReport->link_report = $publicUrl;
                $processReport->save();

                return $processReport;

            break;

            default:
                return '';
            break;
        }
    }

     /**
     * Filtra os processos
     */
    public function filterProcesses(Array $filters, Int $advocateUserId)
    {
        if(empty($filters)) {   
            return Process::where('advocate_user_id', $advocateUserId)->get(HeaderPDFUtils::ATTRIBUTES_PROCESSES_REPORT);
        }

        $processes = Process::where('advocate_user_id', $advocateUserId);

        if(isset($filters['start_date'])) {
            $processes = $processes->where('start_date', $filters['start_date']);
        }

        if(isset($filters['end_date'])) {
            $processes = $processes->where('end_date', $filters['end_date']);
        }

        if(isset($filters['status'])) {

            $processesIds = [];
            $allProcesses = $processes->get();

            foreach ($allProcesses as $process) {

                $historic = $process->historics()->orderBy('modification_date', 'desc')->first();

                if($historic && $historic->status_process === $filters['status']) {
                    array_push($processesIds, $process->id);
                }else{
                    if($process->status == $filters['status']){
                        array_push($processesIds, $process->id);
                    }
                }
            }

            $processes = $processes->whereIn('id', $processesIds);
        }
        
        return $processes->get(HeaderPDFUtils::ATTRIBUTES_PROCESSES_REPORT);
    }

    /**
     * Filtra os clientes
     */
    public function filterClients(Array $filters, Int $advocateUserId) {

        if(empty($filters)) {   
            return Client::where('advocate_user_id', $advocateUserId)->get(HeaderPDFUtils::ATTRIBUTES_CLIENT_REPORT);
        }

        $clients =  Client::where('advocate_user_id', $advocateUserId);
    
        if(isset($filters['birthday'])) {
            $clients = $clients->where('birthday', $filters['birthday']);
        }

        if(isset($filters['registration_date'])) {
            $clients = $clients->where('created_at', 'like', '%'.$filters['registration_date'].'%');
        }

        if(isset($filters['gender'])) {
            $clients = $clients->where('gender', $filters['gender']);
        }

        if(isset($filters['civil_status'])) {
            $clients = $clients->where('civil_status', $filters['civil_status']);
        }

        return $clients->get(HeaderPDFUtils::ATTRIBUTES_CLIENT_REPORT);
    }

    /**
     * Filtra os contratos
     */
    public function filterContracts(Array $filters, Int $advocateUserId) {

        if(empty($filters)) {   
            return Contract::where('advocate_user_id', $advocateUserId)->get(HeaderPDFUtils::ATTRIBUTES_CONTRACT_REPORT);
        }

        $contracts = Contract::where('advocate_user_id', $advocateUserId);

        if(isset($filters['start_date'])) {
            $contracts = $contracts->where('start_date', $filters['start_date']);
        }

        if(isset($filters['finish_date'])) {
            $contracts = $contracts->where('finish_date', $filters['finish_date']);
        }

        if(isset($filters['canceled_at'])) {
            $contracts = $contracts->where('canceled_at', 'like', '%'.$filters['canceled_at'].'%');
        }

        if(isset($filters['status'])) {

            $status = $filters['status'];

            if($status === 'Cancelado') {
                $contracts = $contracts->whereNotNull('canceled_at');
            }
            if($status == 'Ativo') {
                $contracts = $contracts->whereNull('canceled_at')->where('finish_date', '>', Carbon::now()->format('Y-m-d'));
            }
            if($status == 'Inativo'){
                $contracts = $contracts->whereNull('canceled_at')->where('finish_date', '<', Carbon::now()->format('Y-m-d'));
            }
        }

        if(isset($filters['payment_day'])) {
            $contracts = $contracts->where('payment_day', $filters['payment_day']);
        }

        return $contracts->get(HeaderPDFUtils::ATTRIBUTES_CONTRACT_REPORT);
    }

    /**
     * Gera o relatório dos clientes
     */
    public function generateReportClient($clients, $report)
    {
        $logo = User::find($report->advocate_user_id)->logo;

        if(!$logo){
            $logo = env('DEFAULT_LOGO');
        }

        foreach($clients as $client) 
        {   
            $client->birthday = Carbon::parse($client->birthday)->format('d/m/Y');
            $client->created = Carbon::parse($client->created_at, 'GMT')->format('d/m/Y'); 
            $client->cellphone = MaskUtils::maskPhone($client->cellphone);
            $client->cpf = MaskUtils::maskCPF($client->cpf);    
            $client->street = $client->street.','.$client->number. ' - ' .$client->district. ' ('. $client->state.'-'.$client->city.')';    
        }

        $body = $clients->toArray();

        $pdf = PDF::loadView('reports.clients', [ 
                'title'         => $report->name,
                'logo'          => $logo,
                'type_report'   => $report->type,
                'headers'       => HeaderPDFUtils::HEADER_CLIENTS_REPORT,
                'date'          => Carbon::now()->subHours(3)->format('d/m/Y H:i:s'), 
                'rows'          => $body
            ]
        );

        $report->export_format === 'Paisagem' ? $export = 'landscape' : $export = 'portrait';
        $pdf->setPaper('letter', $export);

        return $this->upload($report, $pdf);
    }   

    /**
     * Gera o relatório dos contratos
     */
    public function generateReportContract($contracts, $report)
    {
        $logo = User::find($report->advocate_user_id)->logo;

        if(!$logo){
            $logo = env('DEFAULT_LOGO');
        }

        foreach($contracts as $contract) 
        {   
            $contract->start_date = Carbon::parse($contract->start_date)->format('d/m/Y');
            $contract->finish_date = $contract->finish_date ?  Carbon::parse($contract->finish_date)->format('d/m/Y'): '-';
            $contract->canceled_at = $contract->canceled_at ? Carbon::parse($contract->canceled_at)->format('d/m/Y') : '-';
            $contract->client_id = $contract->client()->first()->name;
            $contract->contract_price = MaskUtils::maskPrice($contract->contract_price);
            $contract->fine_price = MaskUtils::maskPrice($contract->fine_price);
            $contract->agency = 'Agência: '.$contract->agency .' / Conta: '. $contract->account .' / Banco: '. $contract->bank;
        }

        $body = $contracts->toArray();

        $pdf = PDF::loadView('reports.contracts', [ 
                'title'         => $report->name,
                'logo'          => $logo,
                'type_report'   => $report->type,
                'headers'       => HeaderPDFUtils::HEADER_CONTRACTS_REPORT,
                'date'          => Carbon::now()->subHours(3)->format('d/m/Y H:i:s'), 
                'rows'          => $body
            ]
        );

        $report->export_format === 'Paisagem' ? $export = 'landscape' : $export = 'portrait';
        $pdf->setPaper('letter', $export);

        return $this->upload($report, $pdf);
    }   


        /**
     * Gera o relatório dos contratos
     */
    public function generateReportProcess($processes, $report)
    {
        $logo = User::find($report->advocate_user_id)->logo;

        if(!$logo){
            $logo = env('DEFAULT_LOGO');
        }

        foreach($processes as $process) 
        {   
            $process->start_date = Carbon::parse($process->start_date)->format('d/m/Y');
            $process->end_date = $process->end_date ?Carbon::parse($process->end_date)->format('d/m/Y') : '-';
            $process->client_id = $process->client()->first()->name;

            $historic = $process->historics()->orderBy('modification_date', 'desc')->first();

            if($historic) {
                $process->observations = $historic->status_process;
            }else{
                $process->observations = $process->status;
            }
            $process->number = MaskUtils::maskProcessNumber($process->number);
        }

        $body = $processes->toArray();

        $pdf = PDF::loadView('reports.processes', [ 
                'title'         => $report->name,
                'logo'          => $logo,
                'type_report'   => $report->type,
                'headers'       => HeaderPDFUtils::HEADER_PROCESSES_REPORT,
                'date'          => Carbon::now()->subHours(3)->format('d/m/Y H:i:s'), 
                'rows'          => $body
            ]
        );

        $report->export_format === 'Paisagem' ? $export = 'landscape' : $export = 'portrait';
        $pdf->setPaper('letter', $export);

        return $this->upload($report, $pdf);
    }   

    /**
     * Faz upload do relatório gerado na amazon
     */
    public function upload(Report $report, $pdf) 
    {
        $fileName = $report->name;
        $path = 'reports/'.$report->id.'/'.$fileName;
        
        Storage::disk('s3')->deleteDirectory($path);
        Storage::disk('s3')->put($path, $pdf->output());

        return Storage::disk('s3')->url($path);
    }

    /**
     * Deleta o relatório e seus vinculos
     */
    public function deleteReportAndJoins(Report $report)
    {
        $this->client->where('report_id', $report->id)->delete();
        $this->contract->where('report_id', $report->id)->delete();
        $this->process->where('report_id', $report->id)->delete();

        return $report->delete();
    }
}