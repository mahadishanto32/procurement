<?php

use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            [
                'id' => 1,
                'name' => 'Size',
                'description' => 'Size Attributes',
            ],[
                'id' => 2,
                'name' => 'Color',
                'description' => 'Color Attributes',
            ],[
                'id' => 3,
                'name' => 'Year',
                'description' => 'Year Attributes',
            ],[
                'id' => 4,
                'name' => 'Length',
                'description' => 'Length Attributes',
            ],[
                'id' => 5,
                'name' => 'Width',
                'description' => 'Width Attributes',
            ],[
                'id' => 6,
                'name' => 'Models',
                'description' => 'Models Attributes',
            ],
        ];

        $options = [
            [
                'attribute_id' => 1,
                'name' => 'XL',
            ],[
                'attribute_id' => 1,
                'name' => 'L',
            ],[
                'attribute_id' => 1,
                'name' => 'M',
            ],[
                'attribute_id' => 1,
                'name' => 'S',
            ],[
                'attribute_id' => 2,
                'name' => 'Red',
            ],[
                'attribute_id' => 2,
                'name' => 'Blue',
            ],[
                'attribute_id' => 2,
                'name' => 'Green',
            ],[
                'attribute_id' => 2,
                'name' => 'Yellow',
            ],[
                'attribute_id' => 2,
                'name' => 'Black',
            ],[
                'attribute_id' => 2,
                'name' => 'White',
            ],[
                'attribute_id' => 3,
                'name' => '2020',
            ],[
                'attribute_id' => 3,
                'name' => '2021',
            ],[
                'attribute_id' => 3,
                'name' => '2022',
            ],[
                'attribute_id' => 4,
                'name' => '1 Metre',
            ],[
                'attribute_id' => 4,
                'name' => '2 Metre',
            ],[
                'attribute_id' => 4,
                'name' => '3 Metre',
            ],[
                'attribute_id' => 4,
                'name' => '4 Metre',
            ],[
                'attribute_id' => 4,
                'name' => '5 Metre',
            ],[
                'attribute_id' => 5,
                'name' => '1 Metre',
            ],[
                'attribute_id' => 5,
                'name' => '2 Metre',
            ],[
                'attribute_id' => 5,
                'name' => '3 Metre',
            ],[
                'attribute_id' => 6,
                'name' => 'Toyota',
            ],[
                'attribute_id' => 6,
                'name' => 'BMW',
            ],[
                'attribute_id' => 6,
                'name' => 'Tata',
            ],[
                'attribute_id' => 6,
                'name' => 'Corrola',
            ],
        ];

        \App\Models\PmsModels\Attribute::insert($attributes);
        \App\Models\PmsModels\AttributeOption::insert($options);
    }
}
