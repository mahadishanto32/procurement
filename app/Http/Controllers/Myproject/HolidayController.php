<?php

namespace App\Http\Controllers\Myproject;

use App\Http\Controllers\Controller;
use App\Models\MyProject\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        try {
            $title = 'Holiday setup';
            return view('my_project.backend.pages.holiday.index', compact('title'));
        }catch (\Throwable $th) {
            return $this->backWithError($th->getMessage());
        }
    }

    public function getHolidays()
    {
        try {
            return response()->json(Holiday::all());
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function getHoliday(Request $request)
    {
        try {
            $day = Holiday::where(['date' => date('d',strtotime($request->holiday)),'month' =>date('m',strtotime($request->holiday))])->first();
            return response()->json($day);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeHoliday(Request $request)
    {
        try {
            $day = Holiday::where(['date' => date('d',strtotime($request->date)),'month' => date('m',strtotime($request->date))])->first();
            if (!$day) {
                $day = new Holiday();
                $day->name = $request->name;
                $day->full_date = date('Y-m-d', strtotime($request->date));
                $day->date = date('d',strtotime($request->date));
                $day->month = date('m',strtotime($request->date));
                $day->year = date('Y',strtotime($request->date));
                $day->special_holiday = true;
                $day->save();
            } else {
                $day->name = $request->name;
                $day->save();
            }
            return $this->backWithSuccess('Holiday Saved successfully.');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroyHoliday(Request $request)
    {
        $day = Holiday::where(['date' => date('d',strtotime($request->date)),'month' => date('m',strtotime($request->date))])->first();
        try {
            if ($day->special_holiday) {
                $day->delete();
            }
            return $this->backWithSuccess('Holiday destroyed successfully.');
        } catch (\Throwable $th) {
            return $this->backWithError($th->getMessage());
        }
    }
}
