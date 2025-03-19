@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
<?php 
use Illuminate\Support\Facades\Request;
?>
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
                <a href="{{ url('pms/product-management/product/create') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Product"> <i class="las la-plus">Add</i></a>

                <a href="javascript:void(0)" class="btn btn-sm btn-success text-white" data-toggle="tooltip" title="Upload Product Using Xls Sheet" id="uploadFile"> <i class="las la-cloud-upload-alt">Upload Xls File</i></a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" id="dataTable" data-table-name="{{ $title }}" border="1">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Sub Category')}}</th>
                                <th>{{__('SKU')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Attributes')}}</th>
                                {{-- <th>{{__('Brand')}}</th> --}}
                                <th>{{__('UOM')}}</th>
                                <th>{{__('Unit Price')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $product)
                            <tr>
                                <th>{{  ($products->currentpage()-1) * $products->perpage() + $key + 1  }}</th>
                                <td>{{ isset($product->category->category->name) ? $product->category->category->name : '' }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->attributes->pluck('attributeOption.name')->implode('-') }}</td>
                               {{--  <td>{{ $product->brand->name }}</td> --}}
                                <td>{{ isset($product->productUnit)?$product->productUnit->unit_name:'' }}</td>
                                <td>{{ number_format($product->unit_price,2) }}</td>
                                <td class="text-center" style="width: 10%">
                                    <a href="{{ route('pms.product-management.product.edit', $product->id) }}" class="btn btn-xs btn-info rounded-circle"><i class="las la-edit"></i></a>
                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.product-management.product.destroy', $product->id) }}" class="btn btn-xs btn-danger rounded-circle deleteBtn"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                @if($products)
                                    {{$products->links()}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<div class="modal fade bd-example-modal-lg" id="brandUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Product') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.product-management.product.import')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{ url('pms/product-management/product-import-sample') }}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-md mb-3">
                                <input type="file" name="product_file" id="product_file" class="form-control rounded" required aria-label="Large" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-sm btn-success text-white" style="margin-top:32px"><i class="las la-cloud-upload-alt">Upload Xls File</i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";
        const tableContainer = document.getElementById('dataTable').querySelector('tbody');
        const showEmptyTable = () => {
            if(tableContainer.querySelectorAll('tr').length === 0){
                const row = document.createElement('tr');
                row.id = 'emptyRow';
                let colEmpty = document.createElement('td');
                colEmpty.innerHTML = 'No data is available';
                colEmpty.className = 'text-center';
                colEmpty.colSpan = 9;
                row.appendChild(colEmpty);
                tableContainer.appendChild(row);
            } else {
                if(tableContainer.querySelector('#emptyRow')){
                    tableContainer.querySelector('#emptyRow').remove();
                }
            }
        };
        showEmptyTable();

        const showAlert = (status, error) => {
            swal({
                icon: status,
                text: error,
                dangerMode: true,
                buttons: {
                    cancel: false,
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                // if(value) form.reset();
            });
        };

        $('#uploadFile').on('click', function () {
            $('#brandUploadModal').find('form')[0].reset();
            $('#brandUploadModal').modal('show');
        });

        $('.deleteBtn').on('click', function () {
            var element = $(this);
            swal({
                title: "{{__('Are you sure?')}}",
                text: "{{__('Once you delete, You can not recover this data and related files.')}}",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Delete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                    $.ajax({
                        type: 'DELETE',
                        url: element.attr('data-src'),
                        success:function (data) {
                            if(data){
                                showAlert('error', data);
                                return;
                            }

                            element.parent().parent().remove();
                            swal({
                                icon: 'success',
                                text: 'Data deleted successfully',
                                button: false
                            });
                            setTimeout(()=>{
                                swal.close();
                            }, 1500);
                        },
                    });
                    showEmptyTable();
                }
            });
        })
    })(jQuery)
</script>
@endsection
