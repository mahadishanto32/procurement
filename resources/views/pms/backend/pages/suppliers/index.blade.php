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
              @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Purchase-Employee'))
              <li class="top-nav-btn">
                  <a href="{{route('pms.supplier.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Supplier"> <i class="las la-plus"></i>Add</a>

                  <a href="javascript:void(0)" class="btn btn-sm btn-info text-white" data-toggle="tooltip" title="Upload Supplier by xlsx file" id="uploadFile"> <i class="las la-cloud-upload-alt"></i>Upload Supplier</a>
            </li>
            @endif
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>Image</th>
                                <th>{{__('message.Name')}}</th>
                                <th>Trade</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Mobile No')}}</th>
                                <th>{{__('Address')}}</th>
                                <th>{{__('Status')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $key => $supplier)
                            <tr>
                                <th width="6%">{{ $key+1 }}</th>
                                <td>
                                    @if(!empty($supplier->owner_photo) && file_exists(public_path($supplier->owner_photo)))
                                        <img src="{{ asset($supplier->owner_photo)  }}" style="width: 100%;height: 50px">
                                    @endif
                                </td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->trade }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->mobile_no }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td class="text-center">
                                    @if(auth()->user()->hasRole('Purchase-Department'))
                                        <a class="btn btn-{{ $supplier->status == "Active" ? "success" : "danger" }} btn-block btn-xs" data-supplier-id="{{ $supplier->id }}" data-type="{{ $supplier->status }}" onclick="toggleSupplier($(this))"><i class="{{ $supplier->status == "Active" ? "lar la-check-circle" : "la la-ban" }}"></i>&nbsp;{{ $supplier->status == "Active" ? "Active" : "Pending" }}</a>
                                    @else
                                        <a class="btn btn-{{ $supplier->status == "Active" ? "success" : "danger" }} btn-block btn-xs"><i class="{{ $supplier->status == "Active" ? "lar la-check-circle" : "la la-ban" }}"></i>&nbsp;{{ $supplier->status == "Active" ? "Active" : "Pending" }}</a>
                                    @endif
                                    
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pms.supplier.edit', $supplier->id) }}" class="btn btn-info m-1 btn-xs"><i class="las la-edit"></i></a>
                                    <a href="{{ route('pms.supplier.profile', $supplier->id) }}" class="btn btn-info m-1 btn-xs"><i class="las la-user"></i></a>

                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.supplier.destroy', $supplier->id) }}" class="btn btn-danger m-1 btn-xs deleteBtn"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                     <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                @if(count($suppliers)>0)
                                <ul>
                                    {{$suppliers->links()}}
                                </ul>
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
<!-- Modal ------------------------------------------------------------------------->
<div class="modal fade" id="supplierAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierAddModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" data-src="{{ route('pms.supplier.store') }}">
                @csrf
                @method('post')
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger rounded" id="categoryFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->

    <!-- Supplier Upload Modal Start-->
<div class="modal fade bd-example-modal-lg" id="supplierUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Suppliers') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.suppliers.import-excel')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{URL::to('upload/excel/supplier-sample.xlsx')}}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file size is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-md mb-3">
                                <input type="file" name="supplier_file" class="form-control" required id="excelFile" placeholder="Browse Excel file"
                                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            </div>
                        </div>
                        <div class="col-3">

                            <button type="submit" class="btn btn-sm btn-success text-white" style="margin-top:32px"><i class="las la-cloud-upload-alt"></i>Upload Xls File</i></button>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded pull-left" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- Supplier Upload Modal End -->
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";

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
                if(value) form.reset();
            });
        };

        $('#uploadFile').on('click', function () {
            $('#supplierUploadModal').modal('show');
        });

        const modalShow = () => {
            $('#supplierAddModal').modal('show');
            let url = $('#supplierAddModal').find('form').attr('data-src');
            $('#supplierAddModal').find('form').attr('action', url);
        };

        $('#addSupplierBtn').on('click', function () {
            $('#supplierAddModal').find('form')[0].reset();
            $('#supplierAddModal').find('#supplierAddModalLabel').html(`Add new supplier`);
            modalShow()
        });

        $('.editBtn').on('click', function () {
            $.ajax({
                type: 'get',
                url: $(this).attr('data-src'),
                success:function (data) {
                    console.log(data);
                    if(!data.status){
                        showAlert('error', data.info);
                        return;
                    }
                    $('#supplierAddModal').find('form')[0].reset();
                    $('#supplierAddModal').find('form').attr('data-src', data.info.src);
                        // $('#categoryAddModal').find('form').attr('method', data.info.req_type);
                        $('#supplierAddModal').find('form').find('input[name="_method"]').val(data.info.req_type);
                        $('#supplierAddModal').find('form').find('input[name="phone"]').val(data.info.phone);
                        $('#supplierAddModal').find('form').find('input[name="mobile_no"]').val(data.info.mobile_no);
                        $('#supplierAddModal').find('form').find('input[name="name"]').val(data.info.name);
                        $('#supplierAddModal').find('form').find('input[name="email"]').val(data.info.email);
                        $('#supplierAddModal').find('form').find('textarea[name="address"]').val(data.info.address);
                        $('#supplierAddModal').find('form').find('input[name="city"]').val(data.info.city);
                        $('#supplierAddModal').find('form').find('input[name="state"]').val(data.info.state);
                        $('#supplierAddModal').find('form').find('input[name="country"]').val(data.info.country);
                        $('#supplierAddModal').find('form').find('input[name="zipcode"]').val(data.info.zipcode);

                        if(data.info.status)
                        {
//                            console.log(data.info.status)
                            $('#status').select2().val(data.info.status).trigger("change");
                            //$('#supplierAddModal').find('form').find('input[name="status"]').select2('val',data.info.status);
                        }

                        $('#supplierAddModal').find('#supplierAddModalLabel').html(`Edit supplier (${data.info.name})`);
                        modalShow()
                    }
                })
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
                        url: $(this).attr('data-src'),
                        dataType: 'json',
                    })
                    .done(function(response) {
                        showAlert(response.type, response.message);
                        return;
                    });
                }
            });
        });
    })(jQuery);

    function toggleSupplier(element) {
        if(element.attr('data-type') == "Active"){
            var text = "{{__('Once you Inactive a Supplier, It will not be accesible by the system.')}}";
            var buttonText = "Inactive";
        }else if(element.attr('data-type') == "Inactive"){
            var text = "{{__('Once you Active a Supplier, It will be accesible by the system.')}}";
            var buttonText = "Active";
        }
        swal({
            title: "{{__('Are you sure?')}}",
            text: text,
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: true,
                confirm: {
                    text: buttonText,
                    value: true,
                    visible: true,
                    closeModal: true
                },
            },
        }).then((value) => {
            if(value){
                $.ajax({
                    type: 'POST',
                    url: "{{ url('pms/supplier') }}/"+element.attr('data-supplier-id')+"/toggle",
                    dataType: 'json',
                })
                .done(function(response) {
                    swal({
                        icon: 'success',
                        text: response.message,
                        button: false
                    });
                    setTimeout(()=>{
                        swal.close();
                    }, 1500);

                    element.parent().html('<a class="btn btn-'+(response.status == "Active" ? "success" : "danger")+' btn-block btn-sm" data-supplier-id="'+(element.attr('data-supplier-id'))+'" data-type="'+(response.status)+'" onclick="toggleSupplier($(this))"><i class="'+(response.status == "Active" ? "lar la-check-circle" : "la la-ban")+'"></i>&nbsp;'+(response.status == "Active" ? "Active" : "Pending")+'</a>');

                });
            }
        });
    }
</script>
@endsection
