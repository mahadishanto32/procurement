<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Company;

use App,DB;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $title = 'List of Companies';
        try {
            $data = [
                'title' => $title,
                'companies' => Company::all(),
            ];
            return view('accounting.backend.pages.companies.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Company',
            'code' => uniqueCodeWithoutPrefix(2,'companies','code')
        ];

        return view('accounting.backend.pages.companies.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:companies',
            'name' => 'required|unique:companies',
            'owner_name' => 'required',
            'phone' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => 'required',
            'address' => 'required',
            'logo_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
            'banner_file' => ['required','mimes:jpeg,jpg,png,gif', 'max:3072'],
        ]);

        try{
            $company = Company::create($request->all());
            if($request->hasFile('logo_file')){
                $company->logo = $this->fileUpload($request->file('logo_file'), 'upload/company/logo');
                $company->save();
            }

            if($request->hasFile('banner_file')){
                $company->banner = $this->fileUpload($request->file('banner_file'), 'upload/company/banner');
                $company->save();
            }

            return $this->redirectBackWithSuccess("Company has been created successfully", 'accounting.companies.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        $data = [
            'title' => 'Company Profile - #'.$company->name,
            'company' => $company
        ];

        return view('accounting.backend.pages.companies.profile', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Company',
            'company' => Company::findOrFail($id)
        ];

        return view('accounting.backend.pages.companies.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:companies,code,'.$id,
            'name' => 'required|unique:companies,name,'.$id,
            'owner_name' => 'required',
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
            $company = Company::find($id);
            $company->fill($request->all());
            $company->save();

            if($request->hasFile('logo_file')){
                if(!empty($company->logo)){
                    unlink(public_path($company->logo));
                }
                $company->logo = $this->fileUpload($request->file('logo_file'), 'upload/company/logo');
                $company->save();
            }

            if($request->hasFile('banner_file')){
                if(!empty($company->banner)){
                    unlink(public_path($company->banner));
                }
                $company->banner = $this->fileUpload($request->file('banner_file'), 'upload/company/banner');
                $company->save();
            }
            return $this->redirectBackWithSuccess("Company has been updated successfully", 'accounting.companies.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $company = Company::find($id);
            if(!empty($company->logo)){
                unlink(public_path($company->logo));
            }

            if(!empty($company->banner)){
                unlink(public_path($company->banner));
            }
            $delete = Company::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Company has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
