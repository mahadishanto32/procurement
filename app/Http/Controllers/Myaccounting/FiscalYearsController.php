<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\FiscalYear;

use App,DB;
use Illuminate\Support\Facades\Auth;

class FiscalYearsController extends Controller
{
    public function index()
    {
        $title = 'Fiscal Years';
        try {
            $data = [
                'title' => $title,
                'fiscalYears' => FiscalYear::all(),
            ];
            return view('accounting.backend.pages.fiscalYears.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Fiscal Year',
        ];

        return view('accounting.backend.pages.fiscalYears.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:fiscal_years',
            'start' => 'required',
            'end' => 'required',
        ]);

        try{
            $year = FiscalYear::create($request->all());
            $year->closed = 1;
            $year->save();

            return $this->redirectBackWithSuccess("Fiscal Year has been created successfully", 'accounting.fiscal-years.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        $fiscalYear = FiscalYear::findOrFail($id);
        if($fiscalYear->closed == 0){
            $fiscalYear->closed = 1;
            $fiscalYear->save();

            return $this->redirectBackWithSuccess("Fiscal Year has been Closed successfully", 'accounting.fiscal-years.index');
        }else{
            $fiscalYear->closed = 0;
            $fiscalYear->save();

            FiscalYear::whereNotIn('id', [$id])->update(['closed' => 1]);

            return $this->redirectBackWithSuccess("Fiscal Year has been Opened successfully", 'accounting.fiscal-years.index');
        }
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Fiscal Year',
            'fiscalYear' => FiscalYear::findOrFail($id)
        ];

        return view('accounting.backend.pages.fiscalYears.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:fiscal_years,title,'.$id,
            'start' => 'required',
            'end' => 'required',
        ]);

        try{
            FiscalYear::find($id)->fill($request->all())->save();
            return $this->redirectBackWithSuccess("Fiscal Year has been updated successfully", 'accounting.fiscal-years.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            FiscalYear::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Fiscal Year has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
