
@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
<style type="text/css">
    .modal-backdrop{
        position: relative !important;
    }

</style>
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
                <li class="active">{{__($title)}} List</li>
                <li class="top-nav-btn">
                    <a href="{{route('pms.admin.users.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Active Users"> <i class="las la-check"></i>View Active Users</a>
                </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive">

                        <table id="dataTable" class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="1">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Associate Id</th>
                                    <th>Unit</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Deleted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($users[0]))
                                @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->associate_id }}</td>
                                    <td>{{($user->employee ? $user->employee->unit->hr_unit_name : '')}}</td>
                                    <td>
                                        {{ isset($user->employee->department->hr_department_name) ? $user->employee->department->hr_department_name : '' }}
                                    </td>
                                    <td>
                                        {{($user->employee ? $user->employee->designation->hr_designation_name : '')}}
                                    </td>

                                    <td>{{ implode(', ', $user->getRoleNames()->toArray()) }}</td>
                                    <td>{{ date('d-m-Y',strtotime($user->created_at)) }}</td>
                                    <td>{{ date('d-m-Y',strtotime($user->deleted_at)) }}</td>
                                    <td class="text-center">
                                     <a class="btn btn-xs btn-success" href="{{ url('pms/admin/restore-user/'.$user->id) }}"><i class="la la-check"></i>&nbsp;Restore</a>
                                    </td>
                                 </tr>
                                 @endforeach
                                 @endif
                             </tbody>
                         </table>
                         <div class="row">
                            <div class="col-md-12">
                                <div class="la-1x pull-right">
                                    @if(count($users)>0)
                                    <ul>
                                        {{$users->links()}}
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
<div class="modal fade bd-example-modal-md" id="showUserDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">Use Details Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="dataBody">

            </div>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->
@endsection

@section('page-script')

<script>

    function showUserDetails(userId) {

        $('#dataBody').empty().load('{{URL::to(Request()->route()->getPrefix()."/users")}}/'+userId);

        $('#showUserDetailsModal').modal('show');
    }

    function deleteConfirm(id){
        swal({
            title: "{{__('Are you sure?')}}",
            text: "You won't be able to revert this!",
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: true,
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    closeModal: true
                },
            },
        }).then((result) => {
            if (result) {
                $("#"+id).submit();
            }
        })
    }
</script>

<script>
    (function ($) {
        "use script";
        $('[data-toggle="tooltip"]').tooltip();
        const form = document.getElementById('permissionForm');

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

    })(jQuery)
</script>
@endsection