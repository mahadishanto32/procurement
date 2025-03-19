<table>
    <thead>
        <tr>
            <th>category_code</th>
            {{-- <th>brand_code</th> --}}
            <th>name</th>
            <th>tax</th>
            <th>unit_price</th>
            <th>product_unit</th>
            <th>supplier_mobile</th>

            @if(isset($attributes[0]))
            @foreach($attributes as $key => $attribute)
            <th>{{ strtolower($attribute->name) }}</th>
            @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        <tr>
           <td>{{ isset($category->code) ? $category->code : '' }}</td> 
           {{-- <td>{{ isset($brand->code) ? $brand->code : '' }}</td> --}} 
           <td>Product - 01</td> 
           <td>5</td> 
           <td>100</td> 
           <td>{{ isset($unit->unit_name) ? $unit->unit_name : '' }}</td> 
           <td>{{ $supplier_mobile }}</td> 

           @if(isset($attributes[0]))
           @foreach($attributes as $key => $attribute)
           @php
            $option = \App\Models\PmsModels\AttributeOption::where('attribute_id', $attribute->id)->inRandomOrder()->first();
           @endphp
           <td>{{isset($option->id) ? $option->name : ''}}</td>
           @endforeach
           @endif
        </tr>
    </tbody>
</table>