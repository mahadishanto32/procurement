<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\InventoryModels\InventoryDetails;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\Attribute;
use App\Models\PmsModels\AttributeOption;
use App\Models\PmsModels\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;
use App,Auth,DB;

class InventoryStockController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Inventory Stock Report',
            'attributes' => Attribute::has('options')->where('searchable', 'yes')->get(),
        ];

        return view('pms.backend.pages.inventory.inventory-stock.index',$data);
    }

    public function store(Request $request)
    {
        $attributeOptions = array_values(array_filter(array_map(function($value){
            return $value > 0 ? $value : '';
        }, $request->attributesData)));

        $products = Product::when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->whereHas('relInventoryDetails', function($query){
                return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
            });
        });

        if(isset($attributeOptions[0])){
            foreach($attributeOptions as $key => $option){
                $products = $products->whereHas('attributes', function($query) use($option){
                    return $query->where('attribute_option_id', $option);
                });
            }
        }

        $products = $products->get();

        $inventoryDetails = InventoryDetails::when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })->whereIn('product_id', $products->pluck('id')->toArray())->get();

        return view('pms.backend.pages.inventory.inventory-stock.stocks', compact('products', 'inventoryDetails', 'attributeOptions'));
    }

    public function update(Request $request)
    {
        $attributeOptions = array_values(array_filter(array_map(function($value){
            return $value > 0 ? $value : '';
        }, $request->attributesData)));

        $product = Product::find(request()->get('product_id'));
        $inventoryDetails = InventoryDetails::when(isset(auth()->user()->employee->as_unit_id), function($query){
            return $query->where('hr_unit_id',auth()->user()->employee->as_unit_id);
        })->where('product_id', $product->id)->get();

        return view('pms.backend.pages.inventory.inventory-stock.warehouse', compact('product', 'inventoryDetails', 'attributeOptions'));

    }
}
