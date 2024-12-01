<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Interface\UserManagmentInterface;
use App\Interface\UserManagementInterface;

class UserManagementRepository implements UserManagementInterface
{

    function getUser($request){

        return User::where(['id' => $request->id, 'portal_id' => 2])->first();
    }

    function getUsersByRole($request){

        return User::where('user_role_id', $request->user_role_id)->get();
    }

    function usersChart($request){

        //-- declaration 
        $user_role_id   = $request->user_role_id;
        $start_date     = Carbon::parse($request->start_date);
        $end_date       = Carbon::parse($request->end_date);
        $date_range     = $this->getDateRange($start_date, $end_date, 'year');

        //-- retreiving users with the specific role
        $user_type        = Role::find($user_role_id)->name;

        //-- retreiving the count users in a specific role
        $users = User::where('user_role_id', $user_role_id)
                     ->select(
                         DB::raw('COUNT(users.id) as total_users'),
                         DB::raw('DATE_PART(\'year\', created_at) as year')
                     )
                     ->groupBy('year')
                     ->orderBy('year', 'asc')
                     ->get()
                     ->keyBy('year');
                                               
        $result = [];
        foreach ($date_range as $datePeriod) {

            $year = $datePeriod['year'];
            $total_users = $users->has($year) ? $users->get($year)->total_users : 0;
            //-- Prepare the result for this date period
            $result[] = [
                'year'                          => $year,
                'total_number_'.$user_type      => $total_users,
            ];
        }
    
        //-- return response     
        return [
            "data" => $result
        ];
        
    }   
    // ==== helper function to get the date range
    private function getDateRange($start_date, $end_date, $filter) {
        $dates = [];
        $start = $start_date->copy(); // Use Carbon to manipulate dates
        $end   = $end_date->copy();
    
        // Generate dates based on filter
        while ($start->lte($end)) {
            $year = $start->year;
            $dates[] = ['year' => $year];
            $start->addYear();  
        }
        return $dates;
    }

    function getUsersBySearch($request)
    {
        
        if(isset($request->search))
            return DB::table('users')->where('first_name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%')
                         ->orWhere('last_name', 'like', '%' . $request->search . '%')
                         ->where('portal_id', 2)
                         ->get();
                        
                    
        return DB::table('users')->where('portal_id', 2)->get();
    }
    

    function updateUserInfo($request){

        $user = User::find( $request->id);
        $user->first_name       = $request->first_name;
        $user->last_name        = $request->last_name;
        $user->email            = $request->email;
        $user->date_of_birth    = $request->date_of_birth;
        $user->profile_image    = $request->profile_image;
        $user->password         = Hash::make($request->password);
        $user->save();

        return $user;
    }
    
    function updateUserStatus($request){

        DB::table('users')
        ->where('id', $request->id)
        ->update(['is_active' => $request->is_active]);

        return DB::table('users')->where('id', $request->id)->first();
        
    }
    
}
