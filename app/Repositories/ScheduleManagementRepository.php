<?php

namespace App\Repositories;

use App\Models\Schedule;
use Illuminate\Support\Facades\Log;
use App\Interface\ScheduleManagementInterface;

class ScheduleManagementRepository implements ScheduleManagementInterface
{
    function index(){
        return Schedule::all();
    }

    function store($request){
        return Schedule::create([
            'days'          => $request->days,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
        ]);
    }

    function update($request){Log::info('Full Request Data: ',(array) $request);
        $schedule               = Schedule::find($request->id);
        $schedule->days         = $request->days;
        $schedule->start_time   = $request->start_time;
        $schedule->end_time     = $request->end_time;
        $schedule->save();

        return $schedule;
    }

    function delete($request){
        $schedule = Schedule::find($request->id);
        $schedule->delete();

        return $schedule;
    }
}
