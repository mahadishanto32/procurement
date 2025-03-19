<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Company;
use \App\Models\PmsModels\Accounts\CostCentre;

use App,DB;
use Illuminate\Support\Facades\Auth;

class CostCentreController extends Controller
{
    public function index()
    {
        $title = 'Cost Centres';
        try {
            $data = [
                'title' => $title,
                'companies' => Company::all(),
                'costCentres' => CostCentre::when(request()->has('company_id'), function($query){
                    return $query->where('company_id', request()->get('company_id'));
                })->get()
            ];
            return view('accounting.backend.pages.costCentres.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Cost Centre',
            'companies' => Company::all(),
        ];

        return view('accounting.backend.pages.costCentres.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'name' => 'required',
            'phone' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => 'required',
            'address' => 'required',
            'logo_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
            'banner_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
        ]);

        try{
            $costCentre = CostCentre::create($request->all());
            if($request->hasFile('logo_file')){
                $costCentre->logo = $this->fileUpload($request->file('logo_file'), 'upload/cost-centre/logo');
                $costCentre->save();
            }

            if($request->hasFile('banner_file')){
                $costCentre->banner = $this->fileUpload($request->file('banner_file'), 'upload/cost-centre/banner');
                $costCentre->save();
            }

            return $this->redirectBackWithSuccess("Cost Centre has been created successfully", 'accounting.cost-centres.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        if(request()->has('company_id')){
            if(request()->has('cost_centre_id')){
                $costCentre = CostCentre::find(request()->get('cost_centre_id'));
                if($costCentre->company_id == request()->get('company_id')){
                    return $costCentre->code;
                }
            }

            $company = Company::find(request()->get('company_id'));
            $prefix = $company->code.'-';
            $prefix_length = strlen($prefix);
            $max = DB::table('cost_centres')->where('company_id', request()->get('company_id'))->count();
            $new = (int)($max);
            $new++;
            $number_of_zero = 4-strlen($new);
            $zero = str_repeat("0", $number_of_zero);
            return $prefix.$zero.$new;
        }

        $costCentre = CostCentre::findOrFail($id);
        $data = [
            'title' => 'Cost Centre Profile - #'.$costCentre->name,
            'costCentre' => $costCentre
        ];

        return view('accounting.backend.pages.costCentres.profile', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Cost Centre',
            'companies' => Company::all(),
            'costCentre' => CostCentre::findOrFail($id)
        ];

        return view('accounting.backend.pages.costCentres.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required',
            'name' => 'required',
            'phone' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => 'required',
            'address' => 'required',
        ]);

        if($request->hasFile('logo_file')){
            $request->validate([
                'logo_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
            ]);
        }

        if($request->hasFile('banner_file')){
            $request->validate([
                'banner_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
            ]);
        }

        try{
            $costCentre = CostCentre::find($id);
            $costCentre->fill($request->all());
            $costCentre->save();

            if($request->hasFile('logo_file')){
                if(!empty($costCentre->logo)){
                    unlink(public_path($costCentre->logo));
                }
                $costCentre->logo = $this->fileUpload($request->file('logo_file'), 'upload/cost-centre/logo');
                $costCentre->save();
            }

            if($request->hasFile('banner_file')){
                if(!empty($costCentre->banner)){
                    unlink(public_path($costCentre->banner));
                }
                $costCentre->banner = $this->fileUpload($request->file('banner_file'), 'upload/cost-centre/banner');
                $costCentre->save();
            }
            return $this->redirectBackWithSuccess("Cost Centre has been updated successfully", 'accounting.cost-centres.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $costCentre = CostCentre::find($id);
            if(!empty($costCentre->logo)){
                unlink(public_path($costCentre->logo));
            }

            if(!empty($costCentre->banner)){
                unlink(public_path($costCentre->banner));
            }
            $delete = CostCentre::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Cost Centre has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
