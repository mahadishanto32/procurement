@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
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
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active">{{__($title)}}</li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <form action="" method="post">
                                @csrf
                                <div class="from-group">
                                    <label for="dipertment">Department</label>
                                    <select name="dipertment" id="dipertment" class="form-control">
                                        <option value="">Select one</option>
                                        @foreach($departments as $department)
                                        <option {{ $selectedDepartment?($selectedDepartment->hr_department_id === $department->hr_department_id?'selected':''):'' }} value="{{ $department->hr_department_id }}">{{ $department->hr_department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="from-group getUserAsDepartment">
                                    <label for="users">Users</label>
                                    <select name="users" id="users" class="form-control">

                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        (function ($){
            'use script';
            let department = document.querySelector('select[name="dipertment"]');
            let users = document.querySelector('select[name="users"]');

            const setUserOption = (info) => {
                console.log(info)
                let option = document.createElement('option')
                option.value = null;
                option.innerHTML = 'Select one';
                users.appendChild(option);
                $.ajax({
                    type: 'get',
                    url: `${window.location.href}/${info}`,
                    success:(data) => {
                        Array.from(data.data.employees).map(item => {
                            let option = document.createElement('option')
                            option.value = item.id;
                            option.innerHTML = item.name;
                            if (data.data.user){
                                if (data.data.user.id === item.id){
                                    option.selected = true;
                                }
                            }
                            users.appendChild(option);
                        })
                    }
                })
            }

            if (department.value){
                setUserOption(department.value)
            }

            department.onchange = (e) => {
                users.innerHTML = '';
                setUserOption(e.target.value)
            }

            users.onchange = (e) => {
                $.ajax({
                    type: 'post',
                    url: `${window.location.href}`,
                    data: {
                        user: e.target.value
                    },
                    success:(data) => {
                        if (data.status === 200){
                            notify(data.message, 'success')
                        }else {
                            notify(data.message, 'error')
                        }
                    }
                })
            }

        })(jQuery);
    </script>
@endsection
