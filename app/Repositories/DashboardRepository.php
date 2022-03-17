<?php

namespace App\Repositories;

use App\Http\Utils\ProcessesUtils;
use App\Models\AdvocateSchedule;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Contract;
use App\Models\Process;
use Carbon\Carbon;

/**
 * Class DashboardRepository.
 */
class DashboardRepository
{
    public function __construct(Client $client, Contract $contract, Process $process, AdvocateSchedule $schedule)
	{
		$this->client = $client;
        $this->contract = $contract;
        $this->process = $process;
        $this->schedule = $schedule;
	}

    /**
     * Contabiliza os clientes cadastrados
     */
    public function countClients($advocateUserId) 
    {
        return $this->client->where('advocate_user_id', $advocateUserId)->count();
    }

    /**
     * Contabiliza os contratos ativos
     */
    public function countContracts($advocateUserId) 
    {
        return $this->contract->where('advocate_user_id', $advocateUserId)
            ->whereNull('canceled_at')->count();
    }

    /**
     * Contabiliza os contratos ativos
     */
    public function countMeetings($advocateUserId) 
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        return $this->schedule->where('advocate_user_id', $advocateUserId)
            ->where('date', $currentDate)->whereNotNull('client_id')->count();
    }

    /**
     * Obtém os processos pelo status
     */
    public function getProcessesByStatus($advocateUserId)
    {
        $devices = [];
        $labels = [];
        $dataSets = [];
        $backgrounds = [];

        $processes = $this->process->where('advocate_user_id', $advocateUserId)
            ->get()->groupBy('status');

        foreach($processes as $key => $process) 
        {
            foreach($process as $value) {

                $endDate = $value->end_date;
                $currentDate = Carbon::now()->subDay(1)->format('Y-m-d');

                if(!$endDate || $endDate >= $currentDate) 
                {
                    $count = $processes[$key]->count();
                    $color = ProcessesUtils::status[$key];
                    $title = $key;
        
                    $device['title'] = $title;
                    $device["value"] = $count;
                    $device['color'] = $color;
        
                    array_push($dataSets, $count);
                    array_push($backgrounds, $color);
                    array_push($labels, $title);
                    array_push($devices, $device);
                }
            }
        }

        return [
            'devices'       => $devices,
            'labels'        => $labels,
            'data'          => $dataSets,
            'backgrounds'   => $backgrounds
        ];
    }

    public function getContracts($advocateUserId) 
    {
        $contractsActives = [];
        $contractsInactives = [];

        for ($index = 1; $index <= 12; $index++) {
            
            $startDayOfMonth = date('Y-m-d', mktime(0,0,0, $index, 1, date('Y')));
            $endDayOfMonth = Carbon::parse($startDayOfMonth)->endOfMonth()->format('Y-m-d');
            
            $contractActive =  $this->contract->where('advocate_user_id', $advocateUserId)
                ->whereBetween('start_date', [$startDayOfMonth, $endDayOfMonth])->count();
            array_push($contractsActives,$contractActive);

            $contractInactive =  $this->contract->where('advocate_user_id', $advocateUserId)
                ->whereBetween('finish_date', [$startDayOfMonth, $endDayOfMonth])
                ->count();

            array_push($contractsInactives, $contractInactive);
        }

        return [
            'contracts_actives' =>  $contractsActives,
            'contracts_inactives' => $contractsInactives
        ];
    }
    
    public function getClients($advocateUserId)
    {
        $passYear = Carbon::now()->subYear()->format('Y');

        $passClients = $this->client->where('advocate_user_id', $advocateUserId)
            ->where('created_at', 'LIKE', '%'.$passYear.'%')->count();
    
        $currentYear = Carbon::now()->format('Y');
        $currentClients = $this->client->where('advocate_user_id', $advocateUserId)
            ->where('created_at', 'LIKE', '%'.$currentYear.'%')->count();

        return [
            'data'      =>  [$passClients, $currentClients],
            'labels'    =>  [$passYear, $currentYear],
        ];
    }

    public function getAnnualProfit($advocateUserId) 
    {
        $contractsSum = [];
     
        for ($index = 1; $index <= 12; $index++) {

            $startDayOfMonth = date('Y-m-d', mktime(0,0,0, $index, 1, date('Y')));
            $endDayOfMonth = Carbon::parse($startDayOfMonth)->endOfMonth()->format('Y-m-d');

            $sum = $this->contract
                ->where('advocate_user_id', $advocateUserId)
                ->whereBetween('start_date', [$startDayOfMonth, $endDayOfMonth])
                ->whereNotBetween('finish_date', [$startDayOfMonth, $endDayOfMonth])
                ->sum('contract_price');

            array_push($contractsSum, $sum);
        }

        return [
            'data' => $contractsSum
        ];
    }   

    public function getProcessByClient($userId)
    {
        $status = null;
        $lastModification = null;
        $clientId = ClientUser::where('user_id', $userId)->first()->client_id;
      
        $process = $this->process->where('client_id', $clientId)->first();
        
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
        }

        return [
            'status'  => $status,
            'date' => $lastModification
        ];
    }

    public function getContractByClient($userId)
    {
        $startDate = null;
        $endDate = null;
        $clientId = ClientUser::where('user_id', $userId)->first()->client_id;
       
        $contract = $this->contract->where('client_id', $clientId)->orderBy('created_at', 'desc')->first();

        if($contract){
            $startDate = $contract->start_date;
            $endDate = $contract->finish_date;
        }

        return [
            "start_date" => $startDate,
            "end_date"  => $endDate
        ];
    }
}
