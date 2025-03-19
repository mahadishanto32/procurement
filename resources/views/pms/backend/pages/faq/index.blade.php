@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
<style type="text/css" media="screen">
    .select2-container--default .select2-results__option[aria-disabled=true]{
        color: black !important;
        font-weight: bold !important;
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
                <a href="{{ url('pms/return-faq/create') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Faq"> <i class="las la-plus"></i>Add</i></a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{{ route('pms.return-faq.index') }}" method="get" accept-charset="utf-8">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Sub Category') }}:</label> {!! $errors->has('category_id')? '<span class="text-danger text-capitalize">'. $errors->first('category_id').'</span>':'' !!}</p>
                                <div class="select-search-group input-group input-group-md mb-3 d-">
                                    <select name="category_id" id="category_id" class="form-control rounded select2" required onchange="$(this).parent().parent().parent().parent().submit()">
                                        <option selected disabled value="{{ null }}">{{ __('Select One') }}</option>
                                        {!! categoryOptions([], request()->get('category_id')) !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                        <table  id="dataTable" class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="1">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('SL')}}</th>
                                    
                                    <th width="20%">{{__('Sub Category')}}</th>
                                    <th>{{__('Question')}}</th>
                                    
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($questions[0]))
                                @foreach($questions as $key => $question)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $question->category ? $question->category->name : '' }}</td>
                                    <td>{{ $question->name }}</td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-info btn-circle mx-1" href="{{ url('/pms/return-faq/'.$question->id.'/edit')}}"><i class="las la-edit"></i>&nbsp;Edit</a>

                                        <button type="button" class="btn btn-sm btn-danger btn-circle mx-1 deleteBtn" data-src="{{ url('/pms/return-faq/'.$question->id)}}"><i class="las la-trash"></i>&nbsp;Delete</button> 
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        if(response.success){
                            element.parent().parent().remove();
                        }else{
                            showAlert('error', response.message);
                            return;
                        }
                    });
                }
            });
        });
    })(jQuery);
</script>
@endsection
