<?php

namespace App\Repositories;

use App\Models\AdvocateSchedule;
use Carbon\Carbon;

/**
 * Class ScheduleRepository.
 */
class AdvocateScheduleRepository {

    public function __construct(AdvocateSchedule $model)
	{
		$this->model = $model;
	}

    public function getSchedules($advocateUserId, $date)
    {
        $currentDay = Carbon::now()->format('Y-m-d');
        $endMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $scheduleDates = $this->model->where('advocate_user_id', $advocateUserId)
            ->whereBetween('date', [$currentDay, $endMonth])
            ->get()
            ->groupBy('date');
           
        $collection = Collect(); 
        $collection->put('type_day', 2);

        $newSchedules = [];

        foreach ($scheduleDates as $key => $scheduleDate) {

            $haveSchedule = false;
            
            foreach($scheduleDate as $schedule) {

                $client = $schedule->client()->first();

                if($client) {
                    $schedule->client = [
                        "client_id"     => $client->id,
                        "client_name"   => $client->name
                    ];
                }
                
                if($schedule->time_type === 2){
                    $haveSchedule = true;
                }
            }

            if($haveSchedule) {
                $newKey = $key .'$'. 2 .'%'. '#EE96AA';
                $newSchedules[$newKey] = $scheduleDate;

            }else{
                $newKey = $key .'$'. 3 .'%'. '#5ab5cb';
                $newSchedules[$newKey] = $scheduleDate;
            }
        }
        
        return $newSchedules;
    }

    public function create(Array $inputs)
    {
        $advocateUserId = $inputs['advocate_user_id'];
        $timeType = $inputs['time_type'];
        $inputs['horarys'] = json_encode($inputs['horarys']);

        $this->deleteExistingSchedule($advocateUserId, $timeType);
        $inputs['color'] = $this->getColorByType($timeType);

        return $this->model->create($inputs);
    }

    private function deleteExistingSchedule($advocateUserId, $timeType)
    {
        $existSchedules = $this->model->where('advocate_user_id', $advocateUserId)
            ->where('time_type', $timeType)->get();
        
        if(!$existSchedules->isEmpty()) {
            $schedulesIds = $existSchedules->pluck('id')->toArray();
            $this->model->whereIn('id', $schedulesIds)->delete();
        }
    }

    private function getColorByType($timeType)
    {
        switch($timeType) 
        {
            //Dia neutro
            case 1:
                return 'white';

            //Dia agendado
            case 2:
                return '#EE96AA';
        
            //Dia disponivel
            case 3:
                return '#5ab5cb';
            
            default:
                return '#FFFFFF';
        }
    }
}
