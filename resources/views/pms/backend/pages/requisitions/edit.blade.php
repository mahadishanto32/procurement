@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
@php
$modifiedName=false;
@endphp

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}} List</li>
                <li class="top-nav-btn">
                   <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
               </li>
           </ul>
       </div>

       <div class="page-content">
        <div class="">
            <div class="panel panel-info">

                <form action="{{ route('pms.requisition.requisition.update',$requisition->id) }}" method="POST" id="editRequisitionForm">
                    <input type="hidden" name="_method" value="PUT">
                    @csrf
                    <div class="panel-body">
                        <div class="row">


                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="reference">{{ __('Reference No.') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="reference_no" id="reference" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('reference_no',$requisition->reference_no) }}">


                                    @if ($errors->has('reference_no'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('reference_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="date">{{ __('Date') }}:</label> </p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="requisition_date" id="date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ date('Y-m-d',strtotime($requisition->requisition_date)) }}" >
                                </div>
                            </div>
                            @can('project-action')
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="project_id">{{ __('Select Project') }}:</label> </p>

                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="project_id" id="project_id" class="form-control" data-url="{{ route("pms.requisition.load-project-wise-deliverables") }}">
                                        <option value="{{ null }}">{{ __('Select One') }}</option>
                                        @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{$requisition->project_id==$project->id?'selected':''}}>{{ $project->name.' ('.$project->indent_no.')'}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="project_id">{{ __('Select Deliverables') }}:</label> </p>

                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="deliverable_id" id="deliverable_id" class="form-control">
                                        <option value="{{ null }}">{{ __('Select One') }}</option>
                                        @foreach($deliverables as $deliverable)
                                        <option value="{{ $deliverable->id }}" {{$requisition->deliverable_id==$deliverable->id?'selected':''}}>{{ $deliverable->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endcan

                            <div class="col-md-12  table-responsive style-scroll">

                                <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>{{__('Category')}}</th>
                                            <th>{{__('Sub Category')}}</th>
                                            <th width="50%">{{__('Product')}}</th>
                                            <th width="10%">{{__('Qty')}}</th>
                                            @can('department-requisition-edit')
                                            <th width="10%">{{__('Approved Qty')}}</th>
                                            @php 
                                            $modifiedName=true;
                                            @endphp
                                            @endcan

                                            <th class="text-center">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="field_wrapper">

                                        @php
                                        $oldProductIds=[];
                                        @endphp
                                        @forelse($requisition->requisitionItems as $key=>$requisitionItem)
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="category_id" id="category_{{$key}}" class="form-control category">
                                                        <option value="{{ null }}">{{ __('Select Category') }}</option>

                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{$requisitionItem->product->category->parent_id==$category->id?'selected':''}}>
                                                            {{ $category->name.'('.$category->code.')'}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="sub_category_id[]" id="subCategoryId_{{$key}}" class="form-control subcategory" onchange="getProduct($(this))">
                                                        <option value="{{ null }}">{{ __('Select SubCategory') }}</option>

                                                        @if(isset($subCategories[0]))
                                                        @foreach($subCategories as $subCat)
                                                        @if($subCat->parent_id == $requisitionItem->product->category->parent_id)
                                                        <option value="{{ $subCat->id }}" data-selected-product="{{ $requisitionItem->product_id }}" {{ $requisitionItem->product->category_id == $subCat->id ? 'selected' : '' }}>{{ $subCat->name }} ({{ $subCat->code }})</option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                            </td>

                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    @php
                                                    array_push($oldProductIds,$requisitionItem->product->id);
                                                    @endphp
                                                    <select name="product_id[]" id="product_{{$key}}" class="form-control product" required>
                                                        <option value="{{$requisitionItem->product->id}}">{{ __($requisitionItem->product->name) }} ({{ getProductAttributes($requisitionItem->product_id) }})</option>
                                                    </select>
                                                </div>

                                            </td>
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" @if($modifiedName) readonly name="old_qty[]" value="{{ old('qty',$requisitionItem->requisition_qty) }}" @else name="qty[]" value="{{ old('qty',$requisitionItem->qty) }}" @endif  min="1" max="99999999" id="qty_{{$key}}" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required>
                                                </div>
                                            </td>
                                            @can('department-requisition-edit')
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" name="qty[]" min="1" max="99999999" id="qty_{{$key}}" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('qty',$requisitionItem->qty) }}">
                                                </div>
                                            </td>
                                            @endcan

                                            <td>
                                                <a href="javascript:void(0);" id="remove_{{$key}}" class="remove_button btn btn-danger btn-sm" style="margin-right:17px;" title="Remove" >
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                            {{--@endif--}}
                                        </tr>
                                        @empty
                                        @endforelse

                                    </tbody>
                                </table>
                                @if(auth::user()->id ==$requisition->author_id)
                                <a href="javascript:void(0);" style="margin-right:27px;" class="add_button btn btn-sm btn-primary pull-right" title="Add More Product">
                                    <i class="las la-plus"></i>
                                </a>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <p class="mb-1 font-weight-bold"><label for="remarks">{{ __('Notes') }}:</label> {!! $errors->has('remarks')? '<span class="text-danger text-capitalize">'. $errors->first('remarks').'</span>':'' !!}</p>
                                <div class="form-group form-group-lg mb-3 d-">
                                    <textarea rows="3" name="remarks" id="remarks" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm">{!! old('remarks',$requisition->remarks) !!}</textarea>
                                </div>

                                <input type="hidden" name="status" value="{{$requisition->status}}">

                                @if($modifiedName)
                                <input type="hidden" name="approval_qty" value="true">
                                <input type="hidden" name="project_id" value="{{$requisition->project_id}}">
                                <input type="hidden" name="deliverable_id" value="{{$requisition->deliverable_id}}">
                                @else
                                <input type="hidden" name="approval_qty" value="false">
                                @endif
                                <input type="hidden" name="hr_unit_id" value="{{$requisition->hr_unit_id}}">
                                <input type="hidden" name="author_id" value="{{$requisition->author_id}}">
                                <input type="hidden" name="created_by" value="{{$requisition->created_by}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Notes History
                                </button>
                                
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-danger rounded pull-right">{{ __('Update Requisition') }}</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>

            <div class="panel-body">
                <div class="collapse" id="collapseExample">
                  <div class="row">
                    @foreach($requisition->requisitionNoteLogs as $key => $log)
                    <div class="col-md-6 {{ in_array($log->type, ['department-head']) ? 'offset-md-6' : '' }}">
                        <div class="panel">
                            <div class="panel-body">
                                <p>{{ $log->notes }}</p>
                                <br>
                                <small>{{$log->createdBy->name}}&nbsp;&nbsp;|&nbsp;&nbsp;{{ ucwords(implode(' ', explode('-', $log->type))) }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ date('Y-m-d g:i a', strtotime($log->created_at))}}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

@endsection
@section('page-script')
<script type="text/javascript">
    var selectedProductIds=["{{ implode(",",$oldProductIds) }}"];

    function changeSelectedProductIds() {
        selectedProductIds=[];
        $('.product').each(function () {
            selectedProductIds.push($(this).val());
        })
    }

    $(document).ready(function(){
        var maxField = 500;
        var addButton = $('.add_button');
        var x = 1; 
        var wrapper = $('.field_wrapper');
        $(addButton).click(function(){
            x++;

            var fieldHTML = '<tr>\n' +
            '                                            <td>\n' +
            '                                              <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                <select name="category_id" id="category_'+x+'" class="form-control category select2">\n' +
            '                                                    <option value="{{ null }}">{{ __("Select Category") }}</option>\n' +
            '                                                    @foreach($categories as $category)\n' +
            '                                                        <option value="{{ $category->id }}">{{ $category->name."(".$category->code.")"}}</option>\n' +
            '                                                    @endforeach\n' +
            '                                                </select>\n' +
            '                                              </div>\n' +
            '                                            </td>\n' +
            '<td>\n' +
            '                                                    <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                        <select name="sub_category_id[]" id="subCategoryId_'+x+'" class="form-control subcategory" placeholder="Select Sub Category" onchange="getProduct($(this))">\n' +
            '                                                    <option value="{{ null }}">{{ __("Select Subcategory") }}</option>\n' +
            '                                                    @foreach($subCategories as $subCategory)\n' +
            '                                                        <option value="{{ $subCategory->id }}">{{ $subCategory->name."(".$subCategory->code.")"}}</option>\n' +
            '                                                    @endforeach\n' +
            '                                                </select>\n' +
            '                                                    </div>\n' +
            '\n' +
            '                                                </td>'+

            '                                            <td>\n' +
            '\n' +
            '                                                <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                    <select name="product_id[]" id="product_'+x+'" class="form-control select2 product" required>\n' +
            '                                                        <option value="{{ null }}">{{ __("--Select Product--") }}</option>\n' +
            '                                                    </select>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                            </td>\n' +
            '                                            <td>\n' +
            '                                                <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                    <input type="number" name="qty[]" min="1" max="99999999" id="qty_'+x+'" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old("qty") }}">\n' +
            '                                                </div>\n' +
            '                                            </td>\n'+'@if($modifiedName)\n' +
            '<td></td>\n'+'@endif\n' +
            '                                            <td>\n' +
            '                                                <a href="javascript:void(0);" id="remove_'+x+'" class="remove_button btn btn-sm btn-danger" title="Remove" >\n' +
            '                                                    <i class="las la-trash"></i>\n' +
            '                                                </a>\n' +
            '                                            </td>\n' +
            '\n' +
            '                                        </tr>';

            $(wrapper).append(fieldHTML);
            $('#category_'+x, wrapper).select2();
            $('#subCategoryId_'+x, wrapper).select2();
            $('#product_'+x, wrapper).select2();

            getProduct($('#subCategoryId_'+x, wrapper));
        });


            //Once remove button is clicked
            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();
                x--;

                var incrementNumber = $(this).attr('id').split("_")[1];
                var productVal=$('#product_'+incrementNumber).val()

                const index = selectedProductIds.indexOf(productVal);
                if (index > -1) {
                    selectedProductIds.splice(index, 1);
                }
                $(this).parent('td').parent('tr').remove();
                
            });


        });
    </script>

    <script>
        $(document).ready(function() {
            $.each($('.subcategory'), function(index, val) {
                getProduct($(this));
            });

            var wrapper = $('.field_wrapper');

            $(wrapper).on('change', '.category', function (e) {
                changeSelectedProductIds();
                var incrementNumber = $(this).attr('id').split("_")[1];
                //$('#qty_'+incrementNumber).val('');
                $('#product_'+incrementNumber).val('').select2();

                var categoryId = $('#category_' + incrementNumber).val();
                if (categoryId.length === 0) {
                    categoryId = 0;
                }
                $('#subCategoryId_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-subcategory")}}/' + categoryId);

                $('#product_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + categoryId+'?products_id='+selectedProductIds);
            });

            $(wrapper).on('change','.product', function (e) {
                changeSelectedProductIds();
                var incrementNumber = $(this).attr('id').split("_")[1];
                //$('#qty_'+incrementNumber).val('');

                $(this).parent().parent().parent().find('.category').val(parseInt($(this).find(':selected').attr('data-category-id'))).select2();
                $(this).parent().parent().parent().find('.subcategory').val(parseInt($(this).find(':selected').attr('data-sub-category-id'))).select2();
            });

        });

        function getProduct(element){
            var incrementNumber = element.attr('id').split("_")[1];

            changeSelectedProductIds();

            var subcategory_id = $('#subCategoryId_' + incrementNumber).val();
            var selected_product = $('#subCategoryId_' + incrementNumber).find(':selected').attr('data-selected-product');

            if (subcategory_id.length === 0) {
                subcategory_id = 0;
            }
            //$('#qty_'+incrementNumber).val('')
            $('#product_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + subcategory_id+'?products_id='+selectedProductIds+"&selected="+selected_product);
        }


        (function ($){
            "use script";
            $("#project_id").on('change', (e)=> {
                let project = e.target.value;
                $("#deliverable_id").empty();
                if(project) {
                    $.ajax({
                        type: 'get',
                        url: `${e.target.getAttribute("data-url")}/${project}`,
                        success: (data) => {
                            $("#deliverable_id").empty().append(data)
                        },
                    })
                }
            });
        })(jQuery);
    </script>
    @endsection
