<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Attribute;
use App\Models\PmsModels\AttributeOption;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\ProductModel;
use App\Models\PmsModels\ProductModelAttribute;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ProductModelController extends Controller
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
                'title' => "Product Models",
                'products' => Product::all(),
                'models' => ProductModel::where('product_id', request()->get('product_id'))->get(),
            ];
            return view('pms.backend.pages.product-models.index', $data);
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
            'title' => "New Product Model",
            'products' => Product::all(),
            'attributes' => Attribute::has('options')->get(),
        ];
        return view('pms.backend.pages.product-models.create', $data);
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
            'product_id' => 'required',
            'model' => ['required', 'string', 'max:255'],
            'model_name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'string', 'max:255'],
        ]);
        try {
            $productModel = ProductModel::create($request->all());
            if(isset($request->attribute_option_id[0])){
                foreach($request->attribute_option_id as $key => $attribute_option_id){
                    if($attribute_option_id != 0){
                        ProductModelAttribute::create([
                            'product_model_id' => $productModel->id,
                            'attribute_option_id' => $attribute_option_id,
                        ]);
                    }
                }
            }
            return $this->backWithSuccess('Product model created successfully');
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
            'title' => "Edit Product Model",
            'attributes' => Attribute::has('options')->get(),
            'products' => Product::all(),
            'model' => ProductModel::find($id),
        ];
        return view('pms.backend.pages.product-models.edit', $data);
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
        $this->validate($request, [
            'product_id' => 'required',
            'model' => ['required', 'string', 'max:255'],
            'model_name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'string', 'max:255'],
        ]);
        try {
            $productModel = ProductModel::find($id);
            $productModel->fill($request->all())->save();
            ProductModelAttribute::where('product_model_id', $productModel->id)->delete();
            if(isset($request->attribute_option_id[0])){
                foreach($request->attribute_option_id as $key => $attribute_option_id){
                    if($attribute_option_id != 0){
                        ProductModelAttribute::create([
                            'product_model_id' => $productModel->id,
                            'attribute_option_id' => $attribute_option_id,
                        ]);
                    }
                }
            }
            return $this->backWithSuccess('Product model updated successfully');
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
        try {
            ProductModelAttribute::where('product_model_id', $id)->delete();
            ProductModel::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Attribute Option has been deleted"
            ]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
