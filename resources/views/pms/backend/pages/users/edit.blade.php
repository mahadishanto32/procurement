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
                    <a href="#">Home</a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                 <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
             </li>
         </ul><!-- /.breadcrumb -->

     </div>

     <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    {!! Form::model($user,array('route' => ['pms.admin.users.update',$user->id],'method'=>'PUT','class'=>'','files'=>true)) !!}

                    <!--begin::Form-->

                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Name <sup class="text-danger">*</sup></label>
                        <div class="col-6">
                            {!! Form::text('name', $value=old('name',$user->name), array('placeholder' => 'Name','class' => 'form-control','required'=>true)) !!}

                            @if ($errors->has('name'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Phone <sup class="text-danger">*</sup></label>
                        <div class="col-6">
                            {!! Form::text('phone', $value=old('phone',$user->phone), array('placeholder' => 'Phone','class' => 'form-control','required'=>true)) !!}

                            @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Associate Id <sup class="text-danger">*</sup></label>
                        <div class="col-6">
                            {!! Form::text('associate_id', $value=old('associate_id'), array('placeholder' => 'Associate Id','class' => 'form-control','required'=>false)) !!}

                            @if ($errors->has('associate_id'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('associate_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Email</label>
                        <div class="col-6">
                            {!! Form::email('email', $value=old('email',$user->email), array('placeholder' => 'Email address','class' => 'form-control','required'=>false)) !!}

                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Profile Photo</label>
                        <div class="col-6">

                            <label class="slide_upload" for="file">
                                <!--  -->
                                @if(isset($user->profile_photo_path) && file_exists($user->profile_photo_path))
                                <img id="image_load" src="{{asset($user->profile_photo_path)}}" style="width: 150px;height: 150px;cursor:pointer">
                                @else

                                <img id="image_load" src="{{asset('assets/images/user/09.jpg')}}" style="width: 150px; height: 150px;cursor:pointer;">
                                @endif

                            </label>
                            <input id="file" style="display:none" name="profile_photo_path" type="file" onchange="photoLoad(this,this.id)" accept="image/*">


                            @if ($errors->has('profile_photo_path'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('profile_photo_path') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-3 col-form-label text-right">Select Unit</label>
                            <div class="col-6">
                                {!! Form::select('as_unit_id', $units, ($user->employee ? $user->employee->as_unit_id : 0), array('id'=>'','class' => 'form-control','multiple'=>false,'required'=>true)) !!}

                                @if ($errors->has('as_unit_id'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('as_unit_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-3 col-form-label text-right">Select Department</label>
                            <div class="col-6">
                                {!! Form::select('as_department_id', $departments, ($user->employee ? $user->employee->as_department_id : 0), array('id'=>'','class' => 'form-control','multiple'=>false,'required'=>true)) !!}
                                @if ($errors->has('as_department_id'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('as_department_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-3 col-form-label text-right">Select Designation</label>
                            <div class="col-6">
                                {!! Form::select('as_designation_id', $designations, ($user->employee ? $user->employee->as_designation_id : 0), array('id'=>'','class' => 'form-control','multiple'=>false,'required'=>true)) !!}

                                @if ($errors->has('as_designation_id'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('as_designation_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                    <div class="form-group row">
                        <label for="example-text-input" class="col-3 col-form-label text-right">Assign Role(s)</label>
                        <div class="col-6">
                            {!! Form::select('roles[]', $roles,$userRole, array('id'=>'','class' => 'form-control','multiple'=>true,'required'=>true)) !!}
                            @if ($errors->has('roles'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('roles') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                            <label for="example-text-input" class="col-3 col-form-label text-right">Assign Warehouse</label>
                            <div class="col-6">
                                {!! Form::select('warehouse_id[]',$warehouse,$userWarehouse,array('id'=>'','class' => 'form-control','multiple'=>true)) !!}

                                @if ($errors->has('warehouse_id'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('warehouse_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-2">
                        </div>
                        <div class="col-10">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            @can('user-list')
                            <a href="{{route('pms.admin.users.index')}}" class="btn btn-secondary pull-right "> Cancel </a>
                            @endcan
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->

@endsection

@section('page-script')
<script>
    function photoLoad(input,image_load) {
        var target_image='#'+$('#'+image_load).prev().children().attr('id');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $(target_image).attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>
    (function ($) {
        "use script";
        $('[data-toggle="tooltip"]').tooltip();
        const form = document.getElementById('requisitionForm');
        const tableContainer = document.getElementById('dataTable').querySelector('tbody');

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
                if(value)form.reset();
            });
        };



        $('#addRequisitionTypeBtn').on('click', function () {
            $('#requisitionTypeModal').modal('show');
            form.setAttribute('data-type', 'post');
        });

    })(jQuery)
</script>
@endsection