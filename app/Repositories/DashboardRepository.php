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
}
