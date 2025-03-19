@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection
<style type="text/css" media="screen">
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
    .card-body{
        padding-top: 20px !important;
        padding-bottom: 0px !important;
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
</style>
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
                <li class="active">{{__($title)}} List</li>
                <li class="top-nav-btn">
                    <a href="{{url('pms/supplier/profile/'.$supplier->id.(request()->has('tab') ? '?tab='.request()->get('tab') : ''))}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Supplier Profile"> <i class="las la-user"></i>Profile</a>
                  <a href="{{route('pms.supplier.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Supplier List"> <i class="las la-list"></i>List</a>
                </li>
        </ul>
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">Basic Information</a>
                        <a class="nav-item nav-link" id="nav-address-tab" data-toggle="tab" href="#nav-address" role="tab" aria-controls="nav-address" aria-selected="false">Address</a>
                        <a class="nav-item nav-link" id="nav-contact-person-tab" data-toggle="tab" href="#nav-contact-person" role="tab" aria-controls="nav-contact-person" aria-selected="false">Contact person</a>
                        <a class="nav-item nav-link" id="nav-bank-account-tab" data-toggle="tab" href="#nav-bank-account" role="tab" aria-controls="nav-bank-account" aria-selected="false">Bank Account</a>
                      </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                      <div class="tab-pane fade show active" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                        <form method="POST" action="{{ route('pms.supplier.update',$supplier->id) }}?form-type=basic" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        @method('PUT')
                          <div class="form-row mb-5">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Organization Information</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'name', 'text' => ucwords('Name'), 'placeholder' => ucwords('Supplier Name'), 'value' => $supplier->name, 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'phone', 'text' => ucwords('Phone'), 'placeholder' => ucwords('Supplier Phone'), 'value' => $supplier->phone
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'mobile_no', 'text' => ucwords('Mobile No'), 'placeholder' => ucwords('Supplier Mobile No'), 'value' => $supplier->mobile_no
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'email', 'text' => ucwords('Email'), 'placeholder' => ucwords('Supplier Email'), 'value' => $supplier->email
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-2', 'slug' => 'tin', 'text' => ucwords('Tin'), 'placeholder' => ucwords('Supplier Tin'), 'value' => $supplier->tin
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-2', 'slug' => 'trade', 'text' => ucwords('Trade'), 'placeholder' => ucwords('Supplier Trade'), 'value' => $supplier->trade, 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-2', 'slug' => 'bin', 'text' => ucwords('Bin'), 'placeholder' => ucwords('Supplier Bin'), 'value' => $supplier->bin
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-2', 'slug' => 'vat', 'text' => ucwords('Vat'), 'placeholder' => ucwords('Supplier Vat'), 'value' => $supplier->vat
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'website', 'text' => ucwords('website'), 'placeholder' => ucwords('Supplier website'), 'value' => $supplier->website
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-5">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Owner Information</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'owner_name', 'text' => ucwords('name'), 'placeholder' => ucwords('Owner Name'), 'value' => $supplier->owner_name
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'owner_nid', 'text' => ucwords('NID'), 'placeholder' => ucwords('Owner NID'), 'value' => $supplier->owner_nid
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'owner_contact_no', 'text' => ucwords('contact no'), 'placeholder' => ucwords('Owner contact no'), 'value' => $supplier->owner_contact_no
                                            ])

                                            <div class="col-md-3">
                                                <p class="mb-0 font-weight-bold"><label for="owner_photo_file">{{ __('Photo') }}:</label> {!! $errors->has('owner_photo_file')? '<span class="text-danger text-capitalize">'. $errors->first('owner_photo_file').'</span>':'' !!}  @if(!empty($supplier->owner_photo)) <a href="{{ asset($supplier->owner_photo)  }}" target="_blank" style="float:right">View Existing Photo</a> @endif</p>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="file" name="owner_photo_file" id="owner_photo_file" class="form-control rounded" aria-label="Large" placeholder="{{__('Photo')}}" aria-describedby="inputGroup-sizing-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-5">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Others</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mb-0 font-weight-bold"><label for="term_condition">{{ __('Term & Condition') }}:</label> {!! $errors->has('term_condition')? '<span class="text-danger text-capitalize">'. $errors->first('term_condition').'</span>':'' !!}</p>
                                                <div class="form-group form-group-lg mb-3 d-">
                                                    <textarea name="term_condition" id="term_condition" class="form-control rounded summernote" rows="5" placeholder="{{__('Term & Condition Here')}}">{!! old('term_condition', $supplier->term_condition) !!}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mb-0 font-weight-bold"><label for="auth_person_letter_file">{{ __('Authorization letter(pdf or jpg, Max 2 MB)') }}:</label> {!! $errors->has('auth_person_letter_file')? '<span class="text-danger text-capitalize">'. $errors->first('auth_person_letter_file').'</span>':'' !!} @if(!empty($supplier->auth_person_letter)) <a href="{{ asset($supplier->auth_person_letter)  }}" target="_blank">View Existing File</a> @endif</p>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="file" name="auth_person_letter_file" id="auth_person_letter_file" class="form-control rounded" aria-label="Large" placeholder="{{__('Authorization letter')}}" aria-describedby="inputGroup-sizing-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row mb-5">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Payment Terms</h5>
                                        <table class="table table-striped table-bordered miw-500 dac_table mb-1" cellspacing="0" width="100%" id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Payment Term') }}</th>
                                                    <th  width="15%">{{ __('Payment Percent') }}</th>
                                                    <th width="15%">{{__('Day Duration')}}</th>
                                                    <th  width="15%">{{__('Type')}}</th>
                                                    <th class="text-center">{{__('Action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="field_wrapper">
                                            @forelse($supplier->relPaymentTerms as $relPaymentTerm)
                                                <tr>
                                                    <td>
                                                        <div class="input-group input-group-md mb-12 d-">

                                                            <select name="payment_term_id[]" id="paymentTermId_e{{$relPaymentTerm->id}}" class="form-control" required style="width: 100%;">
                                                                <option value="{{ null }}">Select One</option>
                                                                @foreach($paymentTerms as $paymentTerm)
                                                                    <option value="{{ $paymentTerm->id }}" @if($relPaymentTerm->payment_term_id==$paymentTerm->id) selected
                                                                            @endif>{{ $paymentTerm->term}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-md mb-12 d-">

                                                            <input type="number" name="payment_percent[]" value="{{(int)($relPaymentTerm->payment_percent)}}" id="paymentPercent_e{{$relPaymentTerm->id}}" class="form-control payment-percentages" min="1" max="100" placeholder="%" onchange="validatePaymentTerms()" onkeyup="validatePaymentTerms()" required />
                                                        </div>
                                                    </td>
                                                    <td>

                                                        <div class="input-group input-group-md mb-12 d-">
                                                            <input type="number" name="day_duration[]" value="{{(int)($relPaymentTerm->day_duration)}}" id="dayDuration_e{{$relPaymentTerm->id}}" class="form-control day-durations" min="1" max="9999" placeholder="Day" onchange="validatePaymentTerms()" onkeyup="validatePaymentTerms()" required />
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-md mb-12 d-">

                                                            <select name="type[]" id="type_e{{$relPaymentTerm->id}}" class="form-control"  required>
                                                                <option value="{{ null }}">Select One</option>

                                                                <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}"  @if($relPaymentTerm->type==\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE) selected
                                                                    @endif>{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}
                                                                </option>
                                                                <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}"  @if($relPaymentTerm->type==\App\Models\PmsModels\SupplierPaymentTerm::DUE) selected
                                                                        @endif>{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}
                                                                </option>
                                                            </select>

                                                        </div>

                                                    </td>
                                                    <td>
                                                        {{-- <a href="javascript:void(0);" id="remove_e{{$relPaymentTerm->id}}" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">
                                                            <i class="las la-trash"></i>
                                                        </a> --}}
                                                    </td>
                                                </tr>
                                                @empty

                                                @endforelse
                                            </tbody>
                                        </table>
                                        <a href="javascript:void(0);" class="add_button btn btn-sm btn-success mb-2" style="float: right;" title="Add More Term">
                                            <i class="las la-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rounded ml-1 mt-4" >{{ __('Update Supplier') }}</button>
                        </div>
                        </form>
                      </div>
                      <div class="tab-pane fade" id="nav-address" role="tabpanel" aria-labelledby="nav-address-tab">
                        <form method="POST" action="{{ route('pms.supplier.update',$supplier->id) }}?form-type=address">
                        {{ csrf_field() }}
                        @method('PUT')
                          <div class="form-row mb-5">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Corporate Address</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'corporate_road', 'text' => ucwords('road'), 'placeholder' => ucwords('road'), 'value' => (isset($corporateAddress->id) ? $corporateAddress->road : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'corporate_village', 'text' => ucwords('village'), 'placeholder' => ucwords('village'), 'value' => (isset($corporateAddress->id) ? $corporateAddress->village : '')
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'corporate_city', 'text' => ucwords('city'), 'placeholder' => ucwords('city'), 'value' => (isset($corporateAddress->id) ? $corporateAddress->city : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'corporate_zip', 'text' => ucwords('zip'), 'placeholder' => ucwords('zip'), 'value' => (isset($corporateAddress->id) ? $corporateAddress->zip : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'corporate_country', 'text' => ucwords('country'), 'placeholder' => ucwords('country'), 'value' => (isset($corporateAddress->id) ? $corporateAddress->country : ''), 'required' => true
                                            ])
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mb-0 font-weight-bold"><label for="corporate_address">{{ __('Address (If Necessary)') }}:</label> {!! $errors->has('corporate_address')? '<span class="text-danger text-capitalize">'. $errors->first('corporate_address').'</span>':'' !!}</p>
                                                <div class="form-group form-group-lg mb-3 d-">
                                                    <textarea name="corporate_address" id="Address" class="form-control rounded" rows="2" placeholder="{{__('Address (If Necessary)')}}">{!! old('corporate_address', (isset($corporateAddress->id) ? $corporateAddress->address : '')) !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Factory Address</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'factory_road', 'text' => ucwords('road'), 'placeholder' => ucwords('road'), 'value' => (isset($factoryAddress->id) ? $factoryAddress->road : ''), 'required' => false
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'factory_village', 'text' => ucwords('village'), 'placeholder' => ucwords('village'), 'value' => (isset($factoryAddress->id) ? $factoryAddress->village : '')
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'factory_city', 'text' => ucwords('city'), 'placeholder' => ucwords('city'), 'value' => (isset($factoryAddress->id) ? $factoryAddress->city : ''), 'required' => false
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'factory_zip', 'text' => ucwords('zip'), 'placeholder' => ucwords('zip'), 'value' => (isset($factoryAddress->id) ? $factoryAddress->zip : ''), 'required' => false
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'factory_country', 'text' => ucwords('country'), 'placeholder' => ucwords('country'), 'value' => (isset($factoryAddress->id) ? $factoryAddress->country : '')
                                            ])
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mb-0 font-weight-bold"><label for="factory_address">{{ __('Address (If Necessary)') }}:</label> {!! $errors->has('factory_address')? '<span class="text-danger text-capitalize">'. $errors->first('factory_address').'</span>':'' !!}</p>
                                                <div class="form-group form-group-lg mb-3 d-">
                                                    <textarea name="factory_address" id="Address" class="form-control rounded" rows="2" placeholder="{{__('Address (If Necessary)')}}">{!! old('factory_address', (isset($factoryAddress->id) ? $factoryAddress->address : '')) !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rounded ml-1 mt-4" >{{ __('Update Supplier Address') }}</button>
                        </div>
                        </form>
                      </div>
                      <div class="tab-pane fade" id="nav-contact-person" role="tabpanel" aria-labelledby="nav-contact-person-tab">
                        <form method="POST" action="{{ route('pms.supplier.update',$supplier->id) }}?form-type=contact-person">
                        {{ csrf_field() }}
                        @method('PUT')
                          <div class="form-row mb-5">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Contact person (Sales)</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_sales_name', 'text' => ucwords('name'), 'placeholder' => ucwords('name'), 'value' => (isset($contactPersonSales->id) ? $contactPersonSales->name : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_sales_designation', 'text' => ucwords('designation'), 'placeholder' => ucwords('designation'), 'value' => (isset($contactPersonSales->id) ? $contactPersonSales->designation : ''), 'required' => true
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_sales_mobile', 'text' => ucwords('mobile'), 'placeholder' => ucwords('mobile'), 'value' => (isset($contactPersonSales->id) ? $contactPersonSales->mobile : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_sales_email', 'text' => ucwords('email'), 'placeholder' => ucwords('email'), 'value' => (isset($contactPersonSales->id) ? $contactPersonSales->email : '')
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Contact person (After Sales)</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_after_sales_name', 'text' => ucwords('name'), 'placeholder' => ucwords('name'), 'value' => (isset($contactPersonAfterSales->id) ? $contactPersonAfterSales->email : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_after_sales_designation', 'text' => ucwords('designation'), 'placeholder' => ucwords('designation'), 'value' => (isset($contactPersonAfterSales->id) ? $contactPersonAfterSales->email : ''), 'required' => true
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_after_sales_mobile', 'text' => ucwords('mobile'), 'placeholder' => ucwords('mobile'), 'value' => (isset($contactPersonAfterSales->id) ? $contactPersonAfterSales->email : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-6', 'slug' => 'contact_person_after_sales_email', 'text' => ucwords('email'), 'placeholder' => ucwords('email'), 'value' => (isset($contactPersonAfterSales->id) ? $contactPersonAfterSales->email : '')
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rounded ml-1 mt-4" >{{ __('Update Contact Person') }}</button>
                        </div>
                        </form>
                      </div>
                      <div class="tab-pane fade" id="nav-bank-account" role="tabpanel" aria-labelledby="nav-bank-account-tab">
                        <form method="POST" action="{{ route('pms.supplier.update',$supplier->id) }}?form-type=bank-account" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        @method('PUT')
                          <div class="form-row mb-5">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Bank Account</h5>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'bank_account_name', 'text' => ucwords('account name'), 'placeholder' => ucwords('account name'), 'value' => (isset($bankAccount->id) ? $bankAccount->account_name : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'bank_account_number', 'text' => ucwords('account number'), 'placeholder' => ucwords('account number'), 'value' => (isset($bankAccount->id) ? $bankAccount->account_number : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-4', 'slug' => 'bank_swift_code', 'text' => ucwords('swift code'), 'placeholder' => ucwords('swift code'), 'value' => (isset($bankAccount->id) ? $bankAccount->swift_code : ''), 'required' => true
                                            ])
                                        </div>
                                        <div class="row">
                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'bank_name', 'text' => ucwords('bank name'), 'placeholder' => ucwords('bank name'), 'value' => (isset($bankAccount->id) ? $bankAccount->bank_name : ''), 'required' => true
                                            ])

                                            @include('pms.backend.pages.suppliers.element',[
                                                'div' => 'col-md-3', 'slug' => 'bank_branch', 'text' => ucwords('branch'), 'placeholder' => ucwords('branch'), 'value' => (isset($bankAccount->id) ? $bankAccount->branch : ''), 'required' => true
                                            ])
                                            
                                            <div class="col-md-2">
                                                <p class="mb-0 font-weight-bold"><label for="bank_currency">{{ __('Currency') }}:</label> {!! $errors->has('bank_currency')? '<span class="text-danger text-capitalize">'. $errors->first('bank_currency').'</span>':'' !!}</p>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="bank_currency" id="bank_currency" class="form-control rounded">
                                                        <option {{ isset($bankAccount->id) && $bankAccount->currency == "USD" ? 'selected' : '' }}>USD</option>
                                                        <option {{ isset($bankAccount->id) && $bankAccount->currency == "BDT" ? 'selected' : '' }}>BDT</option>
                                                        <option {{ isset($bankAccount->id) && $bankAccount->currency == "EURO" ? 'selected' : '' }}>EURO</option>
                                                        <option {{ isset($bankAccount->id) && $bankAccount->currency == "YEN" ? 'selected' : '' }}>YEN</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-0 font-weight-bold"><label for="bank_security_check_file">{{ __('Bank Guarantee (pdf or jpg)') }}:</label> {!! $errors->has('bank_security_check_file')? '<span class="text-danger text-capitalize">'. $errors->first('bank_security_check_file').'</span>':'' !!}  @if(isset($bankAccount->id) && !empty($bankAccount->security_check)) <a href="{{ asset($bankAccount->security_check)  }}" target="_blank">View Existing File</a> @endif</p>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="file" name="bank_security_check_file" id="bank_security_check_file" class="form-control rounded" aria-label="Large" placeholder="{{__('Bank Guarantee (Security Check)')}}" aria-describedby="inputGroup-sizing-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary rounded mt-4" >{{ __('Update bank Account') }}</button>
                            </div>
                        </form>
                        </div>
                      </div>
                    </div>
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

        $(document).ready(function(){
            var tab = location.href.split('?tab=')[1];
            if(tab !== undefined){
                $('.nav-tabs').find('.nav-link').removeClass('active');
                $('.nav-tabs').find('#nav-'+(tab)+'-tab').addClass('active');

                $('.tab-content').find('.tab-pane').removeClass('show active');
                $('.tab-content').find('#nav-'+(tab)).addClass('show active');
            }

            var maxField = "{{count($paymentTerms)}}";
            var addButton = $('.add_button');
            var x = parseInt('{{ $supplier->relPaymentTerms->count() }}');
            var wrapper = $('.field_wrapper');

            $(addButton).click(function(){
                if(x < maxField) {
                    x++;
                    var fieldHTML = '<tr>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-md mb-12 d-">\n' +
                        '\n' +
                        '                                            <select name="payment_term_id[]" id="paymentTermId_' + x + '" class="form-control" style="width: 100%;">\n' +
                        '                                                <option value="{{ null }}">Select One</option>\n' +
                        '                                                @foreach($paymentTerms as $paymentTerm)\n' +
                        '                                                    <option value="{{ $paymentTerm->id }}">{{ $paymentTerm->term}}</option>\n' +
                        '                                                @endforeach\n' +
                        '                                            </select>\n' +
                        '\n' +
                        '                                        </div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '\n' +
                        '                                        <div class="input-group input-group-md mb-12 d-">\n' +

                        '<input type="number" name="payment_percent[]" id="paymentPercent_'+x+'" class="form-control payment-percentages" min="1" max="100" placeholder="%" onchange="validatePaymentTerms()" onkeyup="validatePaymentTerms()"/></div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-md mb-12 d-">\n' +
                        '<input type="number" name="day_duration[]" id="dayDuration_'+x+'" class="form-control day-durations" min="1" max="9999" placeholder="Day" onchange="validatePaymentTerms()" onkeyup="validatePaymentTerms()"/></div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-md mb-12 d-">\n' +
                        '\n' +
                        '                                            <select name="type[]" id="type_' + x + '" class="form-control">\n' +
                        '                                                <option value="{{ null }}">Select One</option>\n' +
                        '\n' +
                        '                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}\n' +
                        '                                                    </option>\n' +
                        '                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}\n' +
                        '                                                    </option>\n' +
                        '                                            </select>\n' +
                        '\n' +
                        '                                        </div>\n' +
                        '\n' +
                        '                                    </td>\n' +
                        '                                    <td class="text-center">\n' +
                        '                                        <a href="javascript:void(0);" id="remove_1" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">\n' +
                        '                                            <i class="las la-trash"></i>\n' +
                        '                                        </a>\n' +
                        '                                    </td>\n' +
                        '                                </tr>';

                    $(wrapper).append(fieldHTML);
                    $('#paymentTermId_' + x, wrapper).select2();
                }

            });

            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();
                x--;
                $(this).parent('td').parent('tr').remove();
            });

        });

        

    })(jQuery);

    validatePaymentTerms();
    function validatePaymentTerms(){
        var percentages = 0;
        $.each($('.payment-percentages'), function(index, val) {
            var max = parseInt($(this).attr('max'));

            if($(this).val().length > 3){
                $(this).val($(this).val().substring(0,3));
            }

            if($(this).val() > max){
                $(this).val(0);
            }

            percentages += parseInt($(this).val());

            if(percentages > max){
                $(this).val(0);
            }
        });

        $.each($('.day-durations'), function(index, val) {
            var max = parseInt($(this).attr('max'));
            
            if($(this).val().length > 4){
                $(this).val($(this).val().substring(0,4));
            }

            if($(this).val() > max){
                $(this).val(0);
            }
        });
    }
</script>
@endsection
