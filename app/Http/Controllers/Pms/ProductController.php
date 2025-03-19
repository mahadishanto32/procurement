<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Brand;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\ProductUnit;
use App\Models\PmsModels\Attribute;
use App\Models\PmsModels\ProductAttribute;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Product';
            $products = Product::orderby('id','desc')->paginate(20);
            $categories = Category::all();
            $suppliers = Suppliers::where('status', 'Active')->pluck('name','id')->all();
            $brands = Brand::pluck('name','id')->all();
            $unit = ProductUnit::pluck('unit_name','id')->all();

            $prefix = 'P-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $sku = uniqueCode(14,$prefix,'products','id');

            return view('pms.backend.pages.products.index', compact('title', 'products', 'categories', 'suppliers','brands','sku','unit'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    // public function proCat($value='')
    // {
    //    $products = Product::orderby('id','desc')->get();
    //    foreach($products as $product){
    //         $product->suppliers()->sync([2,3,4,5,11,12]);
    //    }
    //    return 'ok';
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $prefix='P-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $sku=uniqueCode(14,$prefix,'products','id');

            $data = [
                'title' => 'New Product',
                'categories' => Category::all(),
                'suppliers' => Suppliers::where('status', 'Active')->pluck('name','id')->all(),
                'brands' => Brand::pluck('name','id')->all(),
                'unit' => ProductUnit::pluck('unit_name','id')->all(),
                'sku' => $sku,
                'attributes' => Attribute::with('options')->get(),
                'categoryOptions' => categoryOptions([], 0),
            ];
            
            return view('pms.backend.pages.products.create', $data);
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
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'string', 'max:255'],
            // 'brand_id' => ['required', 'string', 'max:255'],
            'product_unit_id' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'unit_price' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'sku' => ['required'],
            'supplier' => ['required'],
        ]);

        try {
            $suppliers = $request->supplier;
            $bufferInventory = $request->buffer_inventory;
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            unset($inputs['supplier']);
            unset($inputs['buffer_inventory']);

            $product = Product::create($inputs);
            $product->suppliers()->sync($suppliers);
            if ($bufferInventory){
                $inventorySummary = new InventorySummary();
                $inventorySummary->category_id = $inputs['category_id'];
                $inventorySummary->product_id = $product->id;
                $inventorySummary->buffer_inventory = $bufferInventory;
                $inventorySummary->save();
            }

            if(isset($request->productAttributes[0])){
                foreach($request->productAttributes as $key => $attribute_id){
                    if(isset($request->attribute_option_id[$attribute_id]) && $request->attribute_option_id[$attribute_id] != 0){
                        ProductAttribute::create([
                            'product_id' => $product->id,
                            'attribute_option_id' => $request->attribute_option_id[$attribute_id],
                        ]);
                    }
                }
            }

            return $this->backWithSuccess('Product has been added successfully');
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
    public function show(Product $product)
    {
        try {
            $product->src = route('pms.product-management.product.update',$product->id);
            $product->req_type = 'put';
            $suppliers = [];
            foreach ($product->suppliers as $supplier){
                $suppliers[] = $supplier->id;
            }

            $product->supplier = $suppliers;
            $product->buffer_inventory = $product->relInventorySummary?$product->relInventorySummary->buffer_inventory:null;
            $data = [
                'status' => 'success',
                'info' => $product
            ];

            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        try {
            $data = [
                'title' => 'Edit Product',
                'categories' => Category::all(),
                'suppliers' => Suppliers::where('status', 'Active')->get(['id', 'name']),
                'brands' => Brand::pluck('name','id')->all(),
                'unit' => ProductUnit::pluck('unit_name','id')->all(),
                'attributes' => Attribute::with('options')->get(),
                'product' => $product,
                'productAttributes' => $product->attributes->pluck('attributeOption.attribute_id')->toArray(),
                'existedSuppliers' => $product->suppliers->pluck('pivot.supplier_id')->toArray(),
                'categoryOptions' => categoryOptions([], $product->category_id),
                'categoryAttributeOptions' => isset(json_decode($product->category->attributes, true)[0]) ? json_decode($product->category->attributes, true) : [],
                'categoryAttributes' => Attribute::whereHas('options', function($query) use($product){
                    return $query->whereIn('id', isset(json_decode($product->category->attributes, true)[0]) ? json_decode($product->category->attributes, true) : []);
                })->pluck('id')->toArray()
            ];
            
            return view('pms.backend.pages.products.edit', $data);
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
    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'sku' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'string', 'max:255'],
            // 'brand_id' => ['required', 'string', 'max:255'],
            'product_unit_id' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'tax' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'supplier' => ['required'],
        ]);

        try {
            $suppliers = $request->supplier;
            $bufferInventory = $request->buffer_inventory;
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            unset($inputs['supplier']);
            unset($inputs['buffer_inventory']);

            $product->update($inputs);
            $product->suppliers()->sync($suppliers);
            if ($bufferInventory){
                if ($product->relInventorySummary){
                    $inventorySummary = $product->relInventorySummary;
                }else{
                    $inventorySummary = new InventorySummary();
                    $inventorySummary->category_id = $inputs['category_id'];
                    $inventorySummary->product_id = $product->id;
                }
                $inventorySummary->buffer_inventory = $bufferInventory;
                $inventorySummary->save();
            }

            ProductAttribute::where('product_id', $product->id)->delete();
            if(isset($request->productAttributes[0])){
                foreach($request->productAttributes as $key => $attribute_id){
                    if(isset($request->attribute_option_id[$attribute_id]) && $request->attribute_option_id[$attribute_id] != 0){
                        ProductAttribute::create([
                            'product_id' => $product->id,
                            'attribute_option_id' => $request->attribute_option_id[$attribute_id],
                        ]);
                    }
                }
            }

            return $this->backWithSuccess('Product has been updated successfully');
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
    public function destroy(Product $product)
    {
        try {
            $product->suppliers()->sync([]);
            ProductAttribute::where('product_id', $product->id)->delete();
            $product->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function importProductSample(){
        $data = [
            'attributes' => Attribute::get(['id','name']),
            'category' => Category::has('category')->inRandomOrder()->first(['code']),
            'brand' => Brand::inRandomOrder()->first(['code']),
            'unit' => ProductUnit::inRandomOrder()->first(['unit_name']),
            'supplier_mobile' => Suppliers::where('status', 'Active')->inRandomOrder()->take('3')->pluck('mobile_no')->implode(',')
        ];

        return Excel::download(new \App\Exports\PMS\ProductSample($data), 'Product Upload Sample.xlsx');
    }

    public function importProduct(Request $request)
    {
        $this->validate($request, [
            'product_file'  => 'mimes:xls,xlsx'
        ]);

        //$path = $request->file('product_file')->getRealPath();

        try {

            Excel::import(new ProductImport, $request->product_file);

            return $this->backWithSuccess('Excel Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $error=[];
            foreach ($failures as $failure) {
                $failure->row(); 
                $failure->attribute(); 
                $error[]=$failure->errors(); 
                $failure->values(); 
            }

            return $this->backWithError($error[0][0]);
        }
    }
}
