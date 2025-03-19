<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Tag;

use App,DB;
use Illuminate\Support\Facades\Auth;

class TagsController extends Controller
{
    public function index()
    {
        $title = 'Tags';
        try {
            $data = [
                'title' => $title,
                'tags' => Tag::all(),
            ];
            return view('accounting.backend.pages.tags.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Tag',
        ];

        return view('accounting.backend.pages.tags.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tags',
            'color' => 'required',
            'background' => 'required',
        ]);

        try{
            Tag::create($request->all());
            return $this->redirectBackWithSuccess("Tag has been created successfully", 'accounting.tags.create');
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
            'title' => 'Edit Tag',
            'tag' => Tag::findOrFail($id)
        ];

        return view('accounting.backend.pages.tags.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:tags,title,'.$id,
            'color' => 'required',
            'background' => 'required',
        ]);

        try{
            Tag::find($id)->fill($request->all())->save();
            return $this->redirectBackWithSuccess("Tag has been updated successfully", 'accounting.tags.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            Tag::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Tag has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
