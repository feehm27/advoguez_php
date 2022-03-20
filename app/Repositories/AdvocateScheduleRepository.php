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
        $currentDay = Carbon::now()->subDay()->format('Y-m-d');
        $endMonth = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

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

                if($client && $schedule->time_type === 3) {
                    $schedule->client = [
                        "client_id"     => $client->id,
                        "client_name"   => $client->name
                    ];
                    $haveSchedule = true;
                }

                if($haveSchedule){
                    $scheduleDate = $scheduleDate->where('time_type', 3)->values();
                }
            }

            if($haveSchedule) {
                $newKey = $key .'$'. 3 .'%'. '#EE96AA';
                $newSchedules[$newKey] = $scheduleDate;
            }else{
                $newKey = $key .'$'. 2 .'%'. '#5ab5cb';
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
     * Agenda a reunião para o cliente
     */
    public function scheduleMeetingClient(Array $inputs)
    {
        $existSchedule = $this->model->where('client_id', $inputs['client_id'])
            ->whereDate('date', $inputs['date'])->first();

        if($existSchedule){
            $existSchedule->delete();
        }

        $this->removeAvailableTime($inputs);

        $inputs['horarys'] = json_encode($inputs['horarys']);

        return $this->model->create($inputs);
    }
    
    /**
     * Cancela reunião com o cliente
     */
    private function cancelMetting($inputs)
    {
        $listMails = [];
        $date = $inputs['date'];
        $advocateUserId = $inputs['advocate_user_id'];
        $horarysToRemoved = $inputs['horarys']['hours'];

        $schedulesByClient = $this->model->where('advocate_user_id', $advocateUserId )->where('date', $date)->where('time_type', 3)->get();

        if(!$schedulesByClient->isEmpty()){

            foreach($schedulesByClient as $scheduleClient)
            {   
                $hourClient = json_decode($scheduleClient->horarys)->hours[0];

                if (in_array($hourClient, $horarysToRemoved)) {
                    $mails = [
                        "client_id" => $scheduleClient->client_id,
                        "hour"      => $hourClient
                    ];
                    array_push($listMails, $mails);
                    $scheduleClient->delete();
                }
            }
        }

        $schedulesByAdvocate = $this->model->where('advocate_user_id', $advocateUserId)->where('date', $date)->where('time_type', 1)->first();

        if($schedulesByAdvocate) {
          
            $horarysToShedule = json_decode($schedulesByAdvocate->horarys, true)['hours'];

            foreach($horarysToRemoved as $key => $hour){
                array_push($horarysToShedule, $hour);
            }
        
            $object = array_values($horarysToShedule);
            $schedulesByAdvocate->horarys = json_encode(["hours" => $object]);
            $schedulesByAdvocate->save();
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
