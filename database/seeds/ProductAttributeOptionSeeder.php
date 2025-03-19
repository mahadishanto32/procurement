<?php

use Illuminate\Database\Seeder;

class ProductAttributeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \App\Models\PmsModels\Product::doesntHave('attributes')->get();
        $data = [];
        foreach($products as $key => $product){
            $attributes = \App\Models\PmsModels\Attribute::has('options')->get();
            foreach($attributes as $key => $attribute){
                array_push($data, [
                    'product_id' => $product->id,
                    'attribute_option_id' => \App\Models\PmsModels\AttributeOption::where('attribute_id', $attribute->id)->inRandomOrder()->first()->id,
                ]);
            }
        }
        \App\Models\PmsModels\ProductAttribute::insert($data);
    }
}
