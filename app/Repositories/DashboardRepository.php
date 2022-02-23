<?php

namespace App\Repositories;

use App\Http\Utils\ProcessesUtils;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Process;
use Carbon\Carbon;


/**
 * Class DashboardRepository.
 */
class DashboardRepository
{
    public function __construct(Client $client, Contract $contract, Process $process)
	{
		$this->client = $client;
        $this->contract = $contract;
        $this->process = $process;
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
}
