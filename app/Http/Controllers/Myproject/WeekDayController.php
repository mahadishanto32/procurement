<?php

namespace App\Http\Controllers\Myproject;

use App\Http\Controllers\Controller;
use App\Models\MyProject\WeekDay;
use Illuminate\Http\Request;

class WeekDayController extends Controller
{
    public function index()
    {
        try {
            $weekdays = WeekDay::all();
            $title = 'Day setup';
            return view('my_project.backend.pages.weekday.index', compact('weekdays', 'title'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $weekdays = $request->work_day;
            $workHours = [];
            foreach ($request->hour as $key => $hour){
                if (!$hour){
                    unset($workHours[$key]);
                } else {
                    $workHours[] = $hour;
                }
            }
            foreach ($weekdays as $key => $weekday){
                $week_day = WeekDay::where('name', $weekday)->first();
                $week_day->where('name', $weekday)->update(['work_on'=>true, 'hour'=>$workHours[$key]]);

                if ($week_day->name === $request->report_day){
                    $week_day->update(['report_on'=>true]);
                }else{
                    $week_day->update(['report_on'=>false]);
                }
            }
            return $this->backWithSuccess('Working Day has been setup successfully.');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
