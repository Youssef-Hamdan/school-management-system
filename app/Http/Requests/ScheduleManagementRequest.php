<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleManagementRequest extends FormRequest
{
    function store()
    {   
        return Validator::make(request()->all(),[
            'days'          => 'required|array', 
            'days.*'        => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'    => 'required|date_format:H:i:s',
            'end_time'      => 'required|date_format:H:i:s|after:start_time',
        ]);
    }

    function update($id)
    {    request()->merge(['id'=>$id]);
        return Validator::make(request()->all(),[
            'id'            => 'required|exists:schedules,id,deleted_at,NULL',
            'days'          => 'required|array', 
            'days.*'        => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'    => 'required|date_format:H:i:s',
            'end_time'      => 'required|date_format:H:i:s|after:start_time',
        ]);
    }
    
    function delete($id)
    {   request()->merge(['id'=>$id]);
        return Validator::make(request()->all(),[
            'id'            => 'required|exists:schedules,id,deleted_at,NULL',
        ]);
    }


    
}
