@extends('accounting.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }
    .select2-container--default .select2-results__option[aria-disabled=true] {
        color: #000 !important;
        font-weight:  bold !important;
    }
    .select2-container{
        width:  100% !important;
    }
    tr td{
        padding: 10px 3px 10px 3px !important;
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
                <li class="active">Accounts</li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    <form action="{{ route('accounting.entries.store') }}?type={{ request()->get('type')  }}" method="post" accept-charset="utf-8" class="entry-form">
                    @csrf
                        <div class="row pr-3">
                            <div class="col-md-2">
                                <label for="code"><strong>{{ __('Code') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="code" id="code" value="{{ $code }}" readonly class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="number"><strong>{{ __('Number') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="number" name="number" id="number" value="{{ old('number') }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="date"><strong>{{ __('Date') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" min="{{ $fiscalYear->start }}" max="{{ $fiscalYear->end }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="tag_id"><strong>{{ __('Tag') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="tag_id" id="tag_id" class="form-control rounded">
                                        @if(isset($tags[0]))
                                        @foreach($tags as $key => $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="fiscal_year_id"><strong>{{ __('Fiscal Year') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="fiscal_year_id" id="fiscal_year_id" class="form-control rounded">
                                        <option value="{{ $fiscalYear->id }}">{{ $fiscalYear->title }}&nbsp;|&nbsp;{{ date('d-M-y', strtotime($fiscalYear->start)).' to '.date('d-M-y', strtotime($fiscalYear->end)) }})</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%">Cost Centre</th>
                                            <th style="width: 30%">Ledger</th>
                                            <th style="width: 15%">Debit</th>
                                            <th style="width: 15%">Credit</th>
                                            <th style="width: 10%">Narration</th>
                                            <th style="width: 10%">Balance</th>
                                            <th style="width: 5%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="entries">
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <h5><strong>Total</strong></h5>
                                            </td>
                                            <td style="font-weight: bold;padding-right: 28px !important" class="text-right total-debit"></td>
                                            <td style="font-weight: bold;padding-right: 28px !important" class="text-right total-credit"></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">
                                                <a onclick="add();"><i class="text-success las la-plus-circle" style="transform: scale(2, 2)"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <h5><strong>Difference</strong></h5>
                                            </td>
                                            <td style="font-weight: bold;padding-right: 28px !important" class="text-right debit-difference"></td>
                                            <td style="font-weight: bold;padding-right: 28px !important" class="text-right credit-difference"></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">
                                                
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="notes"><strong>{{ __('Notes') }}:</strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <textarea name="notes" id="notes" class="form-control rounded">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('accounting/entries') }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md btn-submit"><i class="la la-save"></i>&nbsp;Save Entry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script type="text/javascript">
    add();
    add();
    function add() {
        $('.entries').append('<tr>'+
                                '<td>'+
                                   '<select name="cost_centre_id[]" class="form-control cost_centre_id select2">{!! $costCentres !!}</select>'+
                                '</td>'+
                                '<td>'+
                                    '<select name="chart_of_account_id[]" class="form-control chart_of_account_id select2" onchange="Entries()">{!! $chartOfAccountsOptions !!}</select>'+
                                '</td>'+
                                '<td>'+
                                    '<input type="number" name="amount[]" class="form-control debit text-right" onchange="Entries()" onkeyup="Entries()">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="number" name="amount[]" class="form-control credit text-right" onchange="Entries()" onkeyup="Entries()">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="text" name="narration[]" class="form-control narration">'+
                                '</td>'+
                                '<td class="text-right closing-balance"></td>'+
                                '<td class="text-center">'+
                                    '<a onclick="remove($(this))"><i class="text-danger la la-trash" style="transform: scale(2, 2)"></i></a>'+
                                '</td>'+
                            '</tr>');
        Entries();
    }

    function remove(element) {
        element.parent().parent().remove();
        Entries();
    }

    function Entries() {
        $('.chart_of_account_id').select2();

        var total_debit = 0;
        var total_credit = 0;

        $.each($('.entries').find('tr'), function(index, tr) {
            var debit_credit = $(this).find('.chart_of_account_id :selected').attr('data-account-type');
            $(this).find('.closing-balance').html($(this).find('.chart_of_account_id :selected').attr('data-closing-balance'));
            if(debit_credit == "D"){
                $(this).find('.debit').removeAttr('disabled');
                $(this).find('.credit').attr('disabled', 'disabled').val('');
                
                var debit = $(this).find('.debit').val();
                total_debit += parseFloat(debit != "" ? debit : 0);
            }else if(debit_credit == "C"){
                $(this).find('.debit').attr('disabled', 'disabled').val('');
                $(this).find('.credit').removeAttr('disabled');

                var credit = $(this).find('.credit').val();
                total_credit += parseFloat(credit != "" ? credit : 0);
            }else{
                $(this).find('.debit').attr('disabled', 'disabled').val('');
                $(this).find('.credit').attr('disabled', 'disabled').val('');
            }
        });

        $('.total-debit').html(total_debit.toFixed(2));
        $('.total-credit').html(total_credit.toFixed(2));

        if(total_debit == total_credit){
            $('.total-debit').removeClass('bg-danger').addClass('bg-success');
            $('.total-credit').removeClass('bg-danger').addClass('bg-success');
            $('.debit-difference').html('-');
            $('.credit-difference').html('');
        }else{
            $('.total-debit').removeClass('bg-success').addClass('bg-danger');
            $('.total-credit').removeClass('bg-success').addClass('bg-danger');
            if(total_debit > total_credit){
                $('.debit-difference').html('');
                $('.credit-difference').html((total_debit-total_credit).toFixed(2));
            }else{
                $('.credit-difference').html('');
                $('.debit-difference').html((total_credit-total_debit).toFixed(2));
            }
        }
    }

    $(document).ready(function() {
        var form = $('.entry-form');
        var button = $('.btn-submit');
        form.on('submit', function(e){
          e.preventDefault();

          button.prop('disabled', true);
          $.ajax({
              url: form.attr('action'),
              type: form.attr('method'),
              dataType: 'json',
              data: form.serializeArray(),
          })
          .done(function(response) {
              if(response.success){
                location.reload();
              }else{
                toastr.error(response.message);
              }

              button.prop('disabled', false);
          })
          .fail(function(response) {
              $.each(response.responseJSON.errors, function(index, error) {
                   toastr.error(error[0]);
              });

              button.prop('disabled', false);
          });
        });
    });
</script>
@endsection