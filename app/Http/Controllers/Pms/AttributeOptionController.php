<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Attribute;
use App\Models\PmsModels\AttributeOption;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AttributeOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = [
                'title' => "Attribute Options",
                'attributes' => Attribute::all(),
                'attributeOptions' => AttributeOption::where('attribute_id', request()->get('attribute_id'))->get(),
            ];
            return view('pms.backend.pages.attributes.options.index', $data);
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
        $data = [
            'title' => "New Attribute Option",
            'attributes' => Attribute::all(),
        ];
        return view('pms.backend.pages.attributes.options.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'attribute_id' => 'required',
            'name' => ['required', 'string', 'max:255'],
        ]);
        try {
            AttributeOption::create($request->all());
            return $this->backWithSuccess('Attribute Option created successfully');
            

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
    public function show(AttributeOption $attributeOption)
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
        $data = [
            'title' => "Edit Attribute Option",
            'attributes' => Attribute::all(),
            'attributeOption' => AttributeOption::find($id),
        ];
        return view('pms.backend.pages.attributes.options.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttributeOption $attributeOption)
    {
        $this->validate($request, [
            'attribute_id' => 'required',
            'name' => ['required', 'string', 'max:255', Rule::unique('attributes')->ignore($attributeOption->id)],
        ]);
        try {
            $attributeOption->fill($request->all())->save();
            //return $this->backWithSuccess('Attibute Option updated successfully');
            return $this->urlRedirectBack('Attibute Option updated successfully','pms/product-management/attribute-options?attribute_id='.$request->attribute_id,'success');
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
    public function destroy(AttributeOption $attributeOption)
    {
        try {
            $attributeOption->delete();
            return response()->json([
                'success' => true,
                'message' => "Attribute Option has been deleted"
            ]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
