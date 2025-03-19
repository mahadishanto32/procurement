<?php

namespace App\Imports;

use App\Models\PmsModels\Product;
use App\Models\PmsModels\Brand;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Suppliers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection, WithStartRow, WithHeadingRow,WithValidation
{   
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {      
       
        foreach ($rows as $values) 
        {
            $category_id=Category::where('code',$values['category_code'])->first(['id']);
            $brand_id=Brand::where('code',$values['brand_code'])->first(['id']);
            $product_unit_id=\App\Models\PmsModels\ProductUnit::where('unit_name',$values['product_unit'])->first(['id']);
            $suppliers=explode(',', $values['supplier_mobile']);

            $supplier_array=[];
            foreach($suppliers as $phone){
                $supplier=Suppliers::where('mobile_no',$phone)->first(['id']);
                if (!empty($supplier)) {
                   array_push($supplier_array,$supplier->id); 
                }
            }

            if(isset($category_id)){
                $prefix='P-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
                $sku=uniqueCode(14,$prefix,'products','id');
                $product = Product::create([
                    'sku' => $sku,
                    'category_id'=>$category_id->id,
                    // 'brand_id'=>$brand_id->id,
                    'name'=>$values['name'],
                    'tax'=>$values['tax'],
                    'unit_price'=>$values['unit_price'],
                    'product_unit_id' => isset($product_unit_id->id) ? $product_unit_id->id : 0,
                    'created_at'=>date('Y-m-d h:i'),
                ]);
                $product->suppliers()->sync($supplier_array);

                $attributes = \App\Models\PmsModels\Attribute::all();
                if(isset($attributes[0])){
                    foreach($attributes as $key => $attribute){
                        if(isset($values[strtolower($attribute->name)])){
                            $option = \App\Models\PmsModels\AttributeOption::where('attribute_id', $attribute->id)->where('name', 'LIKE', '%'.$values[strtolower($attribute->name)].'%')->first();
                            if(!isset($option->id)){
                                $option = \App\Models\PmsModels\AttributeOption::create([
                                    'attribute_id' => $attribute->id,
                                    'name' => $values[strtolower($attribute->name)],
                                    'description' => $values[strtolower($attribute->name)],
                                ]);
                            }

                            \App\Models\PmsModels\ProductAttribute::create([
                                'product_id' => $product->id,
                                'attribute_option_id' => $option->id,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            'category_code' => ['required', 'string','max:255'],
            // 'brand_code' => ['required','max:255'],
            'name' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
            'supplier_mobile' => ['required','string'],
        ];
    }
}
