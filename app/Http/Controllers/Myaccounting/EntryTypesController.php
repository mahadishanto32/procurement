<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\EntryType;

use App,DB;
use Illuminate\Support\Facades\Auth;

class EntryTypesController extends Controller
{
    public function index()
    {
        $title = 'Entry Types';
        try {
            $data = [
                'title' => $title,
                'entryTypes' => EntryType::all(),
            ];
            return view('accounting.backend.pages.entryTypes.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Entry Type',
        ];

        return view('accounting.backend.pages.entryTypes.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|unique:entry_types',
            'name' => 'required',
            'description' => 'required',
        ]);

        try{
            EntryType::create($request->all());
            return $this->redirectBackWithSuccess("Entry Type has been created successfully", 'accounting.entry-types.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Entry Type',
            'entryType' => EntryType::findOrFail($id)
        ];

        return view('accounting.backend.pages.entryTypes.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|unique:entry_types,label,'.$id,
            'name' => 'required',
            'description' => 'required',
        ]);

        try{
            EntryType::find($id)->fill($request->all())->save();
            return $this->redirectBackWithSuccess("Entry Type has been updated successfully", 'accounting.entry-types.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            EntryType::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Entry Type has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
