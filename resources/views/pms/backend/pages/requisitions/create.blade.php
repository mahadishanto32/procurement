@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
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
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form action="{{ route('pms.requisition.requisition.store') }}" method="post" id="addRequisitionForm" data-src="{{ $requisition?$requisition->id:null }}">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="reference">{{ __('Reference No.') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="reference_no" id="reference" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('reference_no',$refNo) }}">
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
                                        <input type="text" name="requisition_date" id="date" class="form-control rounded air-datepicker" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('date')?old('date'):date('d-m-Y h:i a', time()) }}" >
                                    </div>
                                </div>

                                @can('project-action')
                                    <div class="col-md-3 col-sm-6">
                                        <p class="mb-1 font-weight-bold"><label for="project_id">{{ __('Select Project') }}:</label> </p>

                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="project_id" id="project_id" class="form-control userProject" data-url="{{ route("pms.requisition.load-project-wise-deliverables") }}">
                                                <option value="{{ null }}">{{ __('Select Project') }}</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name.' ('.$project->indent_no.')'}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <p class="mb-1 font-weight-bold"><label for="project_id">{{ __('Select Deliverables') }}:</label> </p>

                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="deliverable_id" id="deliverable_id" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                @endcan


                                <div class="col-md-12 table-responsive style-scroll">

                                    <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{__('Category')}}</th>
                                                <th>{{__('Sub Category')}}</th>
                                                <th width="50%">{{__('Product')}}</th>
                                                <th width="10%">{{__('Quantity')}}</th>
                                                <th class="text-center">{{__('Action')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="field_wrapper">

                                            <tr>
                                                <td>
                                                    <div class="input-group input-group-md mb-3 d-">
                                                        <select name="category_id" id="category_1" class="form-control category">
                                                            <option value="{{ null }}">{{ __('Select Category') }}</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name.'('.$category->code.')'}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="input-group input-group-md mb-3 d-">
                                                        <select name="sub_category_id[]" id="sub_category_id_1" class="form-control subcategory" onchange="getProduct($(this))">
                                                            <option value="{{ null }}">{{ __('Select Subcategory') }}</option>
                                                            @if(isset($subCategories[0]))
                                                            @foreach($subCategories as $key => $subCat)
                                                                <option value="{{ $subCat->id }}">{{ $subCat->name }} ({{ $subCat->code }})</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="product-td">
                                                    <div class="input-group input-group-md mb-3 d-">
                                                        <select name="product_id[]" id="product_1" class="form-control product" required>
                                                            <option value="{{ null }}">{{ __('Select Product') }}</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-md mb-3 d-">
                                                        <input type="number" name="qty[]" min="1" max="99999999" id="qty_1" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" onKeyPress="if(this.value.length==6) return false;" min="1" required value="{{ old('qty') }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" id="remove_1" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                        </tbody>
                                       
                                    </table>
                                    <a href="javascript:void(0);" class="add_button btn btn-sm btn-success pull-right" style="margin-right:17px;" title="Add More Product">
                                        <i class="las la-plus"></i>
                                    </a>
                                   
                                </div>

                                <div class="col-md-12">
                                    <p class="mb-1 font-weight-bold"><label for="remarks">{{ __('Notes') }}:</label> {!! $errors->has('remarks')? '<span class="text-danger text-capitalize">'. $errors->first('remarks').'</span>':'' !!}</p>
                                    <div class="form-group form-group-lg mb-3 d-">
                                        <textarea rows="3" name="remarks" id="remarks" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm">{!! old('remarks') !!}</textarea>
                                    </div>
                                </div>

                                <input type="hidden" name="hr_unit_id" value="{{$unitId}}">
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-info rounded pull-right"><i class="la la-file"></i>{{ __('Add Requisition') }}</button>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
@endsection

@section('page-script')

<script type="text/javascript">
    // (function ($){
        "use strict";
        var selectedProductIds=[];

        function changeSelectedProductIds() {
            selectedProductIds=[];
            $('.product').each(function () { //
                selectedProductIds.push($(this).val());
            })
        }

        $(document).ready(function(){
            var maxField = 200;
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

                    '                                            <td>\n' +
                    '\n' +
                    '                                                <div class="input-group input-group-md mb-3 d-">\n' +
                    '                                                    <select name="sub_category_id[]" id="sub_category_id_'+x+'" class="form-control select2 subcategory" onchange="getProduct($(this))" required>\n' +
                    '                                                    <option value="{{ null }}">{{ __("Select Subcategory") }}</option>\n' +
                    '                                                    @foreach($subCategories as $subCategory)\n' +
                    '                                                        <option value="{{ $subCategory->id }}">{{ $subCategory->name."(".$subCategory->code.")"}}</option>\n' +
                    '                                                    @endforeach\n' +
                    '                                                </select>\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </td>\n' +

                    '                                            <td class="product-td">\n' +
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
                    '                                                    <input type="number" name="qty[]" min="1" max="9999" onKeyPress="if(this.value.length==6) return false;" id="qty_'+x+'" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old("qty") }}">\n' +
                    '                                                </div>\n' +
                    '                                            </td>\n' +
                    '\n' +
                    '                                            <td>\n' +
                    '                                                <a href="javascript:void(0);" id="remove_'+x+'" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">\n' +
                    '                                                    <i class="las la-trash"></i>\n' +
                    '                                                    \n' +
                    '                                                </a>\n' +
                    '                                            </td>\n' +
                    '\n' +
                    '                                        </tr>';

                $(wrapper).append(fieldHTML);
                $('#category_'+x, wrapper).select2();
                $('#sub_category_id_'+x, wrapper).select2();
                $('#product_'+x, wrapper).select2();

                getProduct($('#sub_category_id_'+x, wrapper));
            });

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

        $(document).ready(function() {
            $.each($('.subcategory'), function(index, val) {
                getProduct($(this))
            });

            var wrapper = $('.field_wrapper');
            $(wrapper).on('change', '.category', function (e) {
                var incrementNumber = $(this).attr('id').split("_")[1];

                var categoryId = $('#category_' + incrementNumber).val()
                $('#qty_'+incrementNumber).val('');
                $('#product_'+incrementNumber).val('');

                if (categoryId.length === 0) {
                    categoryId = 0;
                }

                $('#sub_category_id_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-subcategory")}}/' + categoryId);

                $('#product_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + categoryId);
            });

            $(wrapper).on('change','.product', function (e) {
                changeSelectedProductIds();

                var incrementNumber = $(this).attr('id').split("_")[1];
                $('#qty_'+incrementNumber).val('');

                $(this).parent().parent().parent().find('.category').val(parseInt($(this).find(':selected').attr('data-category-id'))).select2();
                $(this).parent().parent().parent().find('.subcategory').val(parseInt($(this).find(':selected').attr('data-sub-category-id'))).select2();
            });

        });


        function getProduct(element){
            var incrementNumber = element.attr('id').split("_")[3];

            changeSelectedProductIds();

            var subcategory_id = $('#sub_category_id_' + incrementNumber).val();
            if (subcategory_id.length === 0) {
                subcategory_id = 0;
            }
            $('#qty_'+incrementNumber).val('')
            $('#product_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + subcategory_id+'?products_id='+selectedProductIds);
        }

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
    // })(jQuery);
</script>
@endsection
