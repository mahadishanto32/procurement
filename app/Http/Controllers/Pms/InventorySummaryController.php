<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\InventoryModels\InventoryDetails;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;
use App,Auth,DB;

class InventorySummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_id = request()->has('category_id') ? request()->get('category_id') : 0;
        $sub_category_id = request()->has('sub_category_id') ? request()->get('sub_category_id') : 0;
        $product_id = request()->has('product_id') ? request()->get('product_id') : 0;
        $warehouse_ids = auth()->user()->relUsersWarehouse->pluck('id')->toArray();
        
        $inventories = InventoryDetails::when($category_id>0,function($query) use($category_id){
            return $query->where('category_id', $category_id)
            ->orWhere(function($query) use($category_id){
                return $query->whereHas('relCategory', function($query) use($category_id){
                    return $query->where('parent_id', $category_id);
                });
            });
        })
        ->when($sub_category_id > 0,function($query) use($sub_category_id){
            return $query->where('category_id', $sub_category_id);
        })
        ->when($product_id>0,function($query) use($product_id){
            return $query->where('product_id',$product_id);
        })
        ->when(isset($warehouse_ids[0]),function($query) use($warehouse_ids){
            return $query->whereIn('warehouse_id',$warehouse_ids);
        })
        ->when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })
        ->where('status','active')
        ->groupBy('product_id')
        ->paginate(30);

        $data = [
            'title' => 'Inventory Summary List',
            'category_id' => $category_id,
            'sub_category_id' => $sub_category_id,
            'product_id' => $product_id,
            'categories' => Category::has('subCategory')->orderBy('name','asc')->get(),
            'inventories' => $inventories
        ];

        return view('pms.backend.pages.inventory.inventory-summary.index',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function getProduct($category_id){
        if(request()->has('sub-categories')){
            return Category::has('category')
            ->when($category_id>0,function($query) use($category_id){
                return $query->where('parent_id',$category_id);
            })->get();
        }

        $products = Product::when($category_id>0,function($query) use($category_id){
            return $query->where('category_id', $category_id)
            ->orWhere(function($query) use($category_id){
                return $query->whereHas('category', function($query) use($category_id){
                    return $query->where('parent_id', $category_id);
                });
            });
        })
        ->when(request()->get('sub_category_id') > 0,function($query){
            return $query->where('category_id', request()->get('sub_category_id'));
        })
        ->orderBy('name','asc')
        ->get();

        $attributes = [];
        if(isset($products[0])){
            foreach($products as $key => $product){
                $attributes[$product->id] = getProductAttributes($product->id);
            }
        }

        return response()->json([
            'products' => $products,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\InventorySummaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function show($data)
    {
        
    }

    /**
     * Show the form for product wise Inventory details.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */

    public function warehouseWiseProductInventoryDetails($product_id)
    {
           try {

            $title = 'Warehouse Wise Product Inventory Details';
            $warehouse_ids = auth()->user()->relUsersWarehouse->pluck('id')->toArray();
            $product = Product::findOrFail($product_id);

            return view('pms.backend.pages.inventory.inventory-summary.warehouse-wise-inventory-details', compact('title','product','warehouse_ids'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function edit(InventorySummary $InventorySummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventorySummary $InventorySummary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventorySummary $InventorySummary)
    {
        //
    }
}
