<?php

namespace App\Repositories;

use App\Mail\CanceledMettingMail;
use App\Models\AdvocateSchedule;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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

    public function create(Array $inputs, $isRemoved = null, $isCancel = null)
    {
        if($isRemoved){
            return $this->removeAvailableTime($inputs);
        }
        
        if($isCancel){
            return $this->cancelMetting($inputs);
        }

        $inputs['horarys'] = json_encode($inputs['horarys']);
        $this->deleteExistingSchedule($inputs['advocate_user_id'], $inputs['time_type'],  $inputs['date']);
        $inputs['color'] = $this->getColorByType($inputs['time_type']);

        return $this->model->create($inputs);
    }

    /**
     * Cancela reunião com o cliente
     */
    private function cancelMetting($inputs)
    {
        $listMails = [];

        $advocateUserId = $inputs['advocate_user_id'];
        $timeType = $inputs['time_type'];
        $date = $inputs['date'];

        $schedules = $this->model->where('advocate_user_id', $advocateUserId)
            ->where('date', $date)->where('time_type', $timeType)->get();
     
        $horarysToRemoved = $inputs['horarys']['hours'];

        foreach ($schedules as $schedule) {

            $horarysToShedule = json_decode($schedule->horarys, true)['hours'];

            foreach ($horarysToRemoved as $key => $hour) {
                
                if (in_array($hour, $horarysToShedule)) {

                    $mails = [ 
                        "client_id" => $schedule->client_id,
                        "hour"      => $hour
                    ];

                    $schedule->delete();
                    array_push($listMails, $mails);
                }
            }
        }   

        $this->sendMails($listMails, $date);
    }

    /**
     * Envia o email de cancelamento da reunião para os clientes
     */
    private function sendMails(Array $listMails = null, $date) {

        if(!empty($listMails)) {

            foreach($listMails as $listMail)
            {
                $client = Client::find($listMail['client_id']);

                $data = [
                    "client_name"  => $client->name,
                    "hour"         => $listMail['hour'],
                    "date"         => Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y')
                ];

                Mail::to($client->email)->send(new CanceledMettingMail($data));
            } 
        }
    }

    /**
     * Remove os horários selecionados para remoção
     */
    private function removeAvailableTime($inputs)
    {
        $schedules = $this->model->where('advocate_user_id', $inputs['advocate_user_id'])
            ->where('date', $inputs['date'])->first();
     
        if($schedules) {
      
            $horarysToShedule = json_decode($schedules->horarys, true)['hours'];
            $horarysToRemoved = $inputs['horarys']['hours'];
        
            foreach ($horarysToShedule as $key => $hour) {
                if (in_array($hour, $horarysToRemoved)){
                    unset($horarysToShedule[$key]);
                }
            }

            if(empty($horarysToShedule)){
                $schedules->delete();
            }else {
                $object = array_values($horarysToShedule);
                $schedules->horarys = json_encode(["hours" => $object]);
                $schedules->save();
            }
        }
    }

    private function deleteExistingSchedule($advocateUserId, $timeType, $date)
    {
        $existSchedules = $this->model->where('advocate_user_id', $advocateUserId)
            ->where('date', $date)->get();
        
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
