<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Attribute;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AttributeController extends Controller
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
                'title' => "Attributes",
                'attributes' => Attribute::all()
            ];
            return view('pms.backend.pages.attributes.index', $data);
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
            'title' => "New Attribute",
        ];
        return view('pms.backend.pages.attributes.create', $data);
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
            'name' => ['required', 'string', 'max:255', 'unique:attributes'],
        ]);
        try {
            $attribute = Attribute::create($request->all());

            $notification = [
                'message' => 'Attribute created successfully',
                'alert-type' => 'success'
            ];
            return redirect('pms/product-management/attribute-options?attribute_id='.$attribute->id)->with($notification);
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
    public function show(Attribute $attribute)
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
            'title' => "Edit Attribute",
            'attribute' => Attribute::find($id)
        ];
        return view('pms.backend.pages.attributes.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', Rule::unique('attributes')->ignore($attribute->id)],
        ]);
        try {
            $attribute->fill($request->all())->save();
            return $this->backWithSuccess('Attibute updated successfully');
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
    public function destroy(Attribute $attribute)
    {
        try {
            $attribute->delete();
            return response()->json([
                'success' => true,
                'message' => "Attribute has been deleted"
            ]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
