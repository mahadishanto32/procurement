<?php

namespace App\Http\Controllers\Pms\Grn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmsModels\Grn\Faq;
use App\Models\PmsModels\Category;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // $categories = Category::has('category')->get();
            // $array = [
            //     "Why You need to return this product?",
            //     "Is this product are broken?",
            //     "Is any product are missing?",
            // ];
            // foreach($categories as $key => $category){
            //     foreach($array as $key => $faq){
            //         Faq::create([
            //             'category_id' => $category->id,
            //             'name' => $faq,
            //         ]);
            //     }
            // }

            $title = 'Faq';
            $questions = Faq::when(request()->has('category_id'), function($query){
                return $query->where('category_id', request()->get('category_id'));
            })->get();
            return view('pms.backend.pages.faq.index', compact('title', 'questions'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $title = 'Create Faq';
            return view('pms.backend.pages.faq.create', compact('title'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        try {
            $faq = Faq::create($request->all());
            return $this->backWithSuccess('Question Saved Successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $title = 'Edit Faq';
            $question = Faq::findOrFail($id);
            return view('pms.backend.pages.faq.edit', compact('title', 'question'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        try {
            $faq = Faq::findOrFail($id);
            $faq->category_id = $request->category_id;
            $faq->name = $request->name;
            $faq->save();

            return $this->backWithSuccess('Question updated Successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        try {
            $faq->delete();

            return response()->json([
                'success' => true
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }

    }
}
