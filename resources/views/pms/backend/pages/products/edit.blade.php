@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }
    .bordered{
        border: 1px #ccc solid
    }
    .floating-title{
        position: absolute;
        top: -13px;
        left: 15px;
        background: white;
        padding: 0px 5px 5px 5px;
        font-weight: 500;
    }

    .label{
        font-weight:  bold !important;
    }

    .tab-pane{
        padding-top: 15px;
    }

    .select2-container{
        width:  100% !important;
    }

    .select2-container--default .select2-results__option[aria-disabled=true]{
        color: black !important;
        font-weight: bold !important;
    }
</style>
@endsection
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                </li>
                <li><a href="#">PMS</a></li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    {{-- <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">Basic Information</a>
                        <a class="nav-item nav-link" id="nav-attributes-tab" data-toggle="tab" href="#nav-attributes" role="tab" aria-controls="nav-attributes" aria-selected="false">Attributes</a>
                      </div>
                    </nav> --}}

                    <form action="{{ route('pms.product-management.product.update', $product->id) }}?tab={{ request()->get('tab') }}" method="post">
                    @csrf
                    @method('PUT')
                        {{-- <div class="tab-content" id="nav-tabContent">
                          <div class="tab-pane fade show active" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab"> --}}
                            <div class="form-row">
                                <div class="col-md-2">
                                    <p class="mb-1 font-weight-bold"><label for="sku">{{ __('SKU') }}:</label> {!! $errors->has('sku')? '<span class="text-danger text-capitalize">'. $errors->first('sku').'</span>':'' !!}</p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="sku" id="sku" class="form-control rounded" aria-label="Large" placeholder="{{__('Product SKU')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('sku')?old('sku'):$product->sku }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> {!! $errors->has('name')? '<span class="text-danger text-capitalize">'. $errors->first('name').'</span>':'' !!}</p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Product Name')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('name', $product->name) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Category') }}:</label> {!! $errors->has('category_id')? '<span class="text-danger text-capitalize">'. $errors->first('category_id').'</span>':'' !!}</p>
                                    <div class="select-search-group input-group input-group-md mb-3 d-">
                                        <select name="category_id" id="category_id" class="form-control rounded select2" required onchange="checkAttributes()">
                                            {!! $categoryOptions !!}
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="brand_id">{{ __('Brand') }}:</label> 
                                        {!! $errors->has('brand_id')? '<span class="text-danger text-capitalize">'. $errors->first('brand_id').'</span>':'' !!}</p>
                                        <div class="select-search-group  input-group input-group-md mb-3 d-">
                                           {!! Form::Select('brand_id',$brands,old('brand_id', $product->brand_id),['id'=>'brand_id', 'class'=>'form-control selectheighttype select2']) !!}
                                       </div>
                                   </div> --}}

                                   <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="supplier">{{ __('Safety Stock') }}:</label> {!! $errors->has('buffer_inventory')? '<span class="text-danger text-capitalize">'. $errors->first('buffer_inventory').'</span>':'' !!}</p>
                                    <div class="select-search-group input-group input-group-md mb-3 d-">
                                        <input type="number" name="buffer_inventory" id="bufferInventory" class="form-control rounded" aria-label="Large" placeholder="{{__('Safety Stock Limit')}}" aria-describedby="inputGroup-sizing-sm" value="{{ old('buffer_inventory', ($product->relInventorySummary ? $product->relInventorySummary->buffer_inventory : 0)) }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="product_unit_id">{{ __('UOM') }}:</label> 
                                        {!! $errors->has('product_unit_id')? '<span class="text-danger text-capitalize">'. $errors->first('product_unit_id').'</span>':'' !!}</p>
                                        <div class="select-search-group  input-group input-group-md mb-3 d-">
                                           {!! Form::Select('product_unit_id',$unit,old('product_unit_id', $product->product_unit_id),['id'=>'product_unit_id', 'class'=>'form-control selectheighttype select2']) !!}
                                       </div>
                                   </div>
                                   <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="unit_price">{{ __('Unit Price') }}:</label> {!! $errors->has('unit_price')? '<span class="text-danger text-capitalize">'. $errors->first('unit_price').'</span>':'' !!}</p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="number" name="unit_price" id="unit_price" class="form-control rounded" aria-label="Large" placeholder="{{__('Product unit price')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('unit_price', $product->unit_price) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 font-weight-bold"><label for="tax">{{ __('Tax') }}:</label> {!! $errors->has('tax')? '<span class="text-danger text-capitalize">'. $errors->first('tax').'</span>':'' !!}</p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="number" name="tax" id="tax" class="form-control rounded" aria-label="Large" placeholder="{{__('Product tax')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('tax', $product->tax) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="attributes">Product Attributes:</label>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="productAttributes[]" id="attributes" class="form-control rounded product-attributes" multiple data-placeholder="Choose Product Attributes" onchange="updateAttributes()">
                                            @if(isset($attributes[0]))
                                            @foreach($attributes as $key => $attribute)
                                                <option value="{{ $attribute->id }}" {{ in_array($attribute->id, $categoryAttributes) && in_array($attribute->id, $productAttributes) ? 'selected' : '' }} {{ !in_array($attribute->id, $categoryAttributes) ? 'hidden' : '' }}>{{ $attribute->name }}</attribute>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        @if(isset($attributes[0]))
                                        @foreach($attributes as $key => $attribute)
                                            <div class="col-md-2 mb-3 attributes attribute-{{ $attribute->id }}" style="display: {{ in_array($attribute->id, $categoryAttributes) && in_array($attribute->id, $productAttributes) ? 'block' : 'none' }}">
                                                <label for="attribute-{{ $attribute->id }}">{{ $attribute->name }}:</label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="attribute_option_id[{{ $attribute->id }}]" id="attribute-{{ $attribute->id }}" class="form-control rounded attribute-options">
                                                        <option value="0">Not Required</option>
                                                        @if(isset($attribute->options[0]))
                                                        @foreach($attribute->options as $key => $option)
                                                            <option value="{{ $option->id }}" {{ in_array($option->id, $categoryAttributeOptions) && $product->attributes->where('attribute_option_id', $option->id)->count() > 0 ? 'selected' : '' }}  {{ !in_array($option->id, $categoryAttributeOptions) ? 'hidden' : '' }}>{{ $option->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div> 
                                            </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <p class="mb-1 font-weight-bold"><label for="supplier">{{ __('Supplier') }}:</label> {!! $errors->has('supplier')? '<span class="text-danger text-capitalize">'. $errors->first('supplier').'</span>':'' !!}</p>
                                    <div class="select-search-group input-group input-group-md mb-3 d-">
                                        <select name="supplier[]" id="supplier" class="form-control rounded select2" multiple style="width: 100%">
                                            @if(isset($suppliers[0]))
                                            @foreach($suppliers as $key => $supplier)
                                            <option value="{{ $supplier->id }}" {{ in_array($supplier->id, $existedSuppliers) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                          {{-- </div>
                          <div class="tab-pane fade" id="nav-attributes" role="tabpanel" aria-labelledby="nav-attributes-tab"> --}}
                            
                          {{-- </div> --}}
                          <a class="btn btn-secondary rounded" href="{{ url('pms/product-management/product') }}"><i class="la la-times"></i>&nbsp;{{ __('Close') }}</a>
                          <button type="submit" class="btn btn-success rounded"><i class="fa fa-check"></i>&nbsp;{{ __('Save Product Information') }}</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script type="text/javascript">
    $(document).ready(function(){
        var tab = location.href.split('?tab=')[1];
        if(tab !== undefined){
            $('.nav-tabs').find('.nav-link').removeClass('active');
            $('.nav-tabs').find('#nav-'+(tab)+'-tab').addClass('active');

            $('.tab-content').find('.tab-pane').removeClass('show active');
            $('.tab-content').find('#nav-'+(tab)).addClass('show active');
        }

        $('.product-attributes').select2({
           templateResult: function(option) {
              if(option.element && (option.element).hasAttribute('hidden')){
                 return null;
              }
              return option.text;
           }
        });

        $('.attribute-options').select2({
           templateResult: function(option) {
              if(option.element && (option.element).hasAttribute('hidden')){
                 return null;
              }
              return option.text;
           }
        });
    });
    
    function checkAttributes() {
        if($('#category_id').find(':selected').attr('data-attributes') != undefined){
            var attributes = $('#category_id').find(':selected').attr('data-attributes').split(',');
            var attributeOptions = $('#category_id').find(':selected').attr('data-attribute-options').split(',');

            $.each($(".product-attributes"), function(name, attribute_id) {
              $.each($(this).find('option'), function(index, val) {
                   if($.inArray($(this).attr('value'), attributes) != -1){
                    $(".product-attributes option[value='" + $(this).attr('value') + "']").removeAttr('hidden');
                   }else{
                    $(".product-attributes option[value='" + $(this).attr('value') + "']").attr('hidden', 'hidden');
                   }
              });
              
              $(this).select2({
               templateResult: function(option) {
                  if(option.element && (option.element).hasAttribute('hidden')){
                     return null;
                  }
                  return option.text;
               }
              });
            });

            $.each($('.attribute-options'), function(index, val) {
                $.each($(this).find('option'), function(index, val) {
                   if($.inArray($(this).attr('value'), attributeOptions) != -1){
                    $(this).parent().find("option[value='" + $(this).attr('value') + "']").removeAttr('hidden');
                   }else{
                    $(this).parent().find("option[value='" + $(this).attr('value') + "']").attr('hidden', 'hidden');
                   }
                });

                $(this).select2({
                    templateResult: function(option) {
                      if(option.element && (option.element).hasAttribute('hidden')){
                         return null;
                      }
                      return option.text;
                    }
                });
            });

            $(".product-attributes").val("").select2();

            $('.product-attributes').select2({
                allowClear: true,
                templateResult: function(option) {
                  if(option.element && (option.element).hasAttribute('hidden')){
                    return null;
                  }

                  return option.text;
                },
            });
        }else{
            $.each($(".product-attributes"), function(name, attribute_id) {
              $.each($(this).find('option'), function(index, val) {
                $(".product-attributes option[value='" + $(this).attr('value') + "']").removeAttr('hidden');
              });
              
              $(this).select2({
               templateResult: function(option) {
                  return null;
               }
              });
            });
        }
    }

    function updateAttributes(){
        var attributes = $("#attributes :selected").map(function(i, el) {
            return $(el).val();
        }).get();

        $('.attributes').hide();
        $.each(attributes, function(index, attribute) {
            $('.attribute-'+attribute).show();
        });
    }
</script>
@endsection