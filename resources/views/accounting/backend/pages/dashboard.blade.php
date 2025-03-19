@extends('accounting.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style>
.avatar-130 {
border-radius: 10% !important;
object-fit: contain !important;
}
.future-services { margin-bottom: 45px; }
.iq-fancy-box { box-shadow: 0 0px 90px 0 rgba(0, 0, 0, .04); position: relative; top: 0; -webkit-transition: all 0.5s ease-out 0s; -moz-transition: all 0.5s ease-out 0s; -ms-transition: all 0.5s ease-out 0s; -o-transition: all 0.5s ease-out 0s; transition: all 0.5s ease-out 0s; padding: 50px 30px; overflow: hidden; position: relative; margin-bottom: 30px; -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; }
.iq-fancy-box .iq-icon { font-size: 36px; border-radius: 90px; display: inline-block; height: 86px; width: 86px; margin-bottom: 15px; line-height: 86px; text-align: center; color: #ffffff; background: #089bab; -webkit-transition: all .5s ease-out 0s; -moz-transition: all .5s ease-out 0s; -ms-transition: all .5s ease-out 0s; -o-transition: all .5s ease-out 0s; transition: all .5s ease-out 0s; }
.iq-fancy-box:hover { box-shadow: 0 44px 98px 0 rgba(0, 0, 0, .12); top: -8px; }
.iq-fancy-box .fancy-content h4 { z-index: 9; position: relative; padding-bottom: 5px }
.iq-fancy-box .fancy-content p { margin-bottom: 0 }
.iq-fancy-box .future-img i { font-size: 45px; color: #089bab; }
.feature-effect-box { box-shadow: 0px 7px 22px 0px rgba(0, 0, 0, 0.06); padding: 10px 15px; margin-bottom: 30px; position: relative; top: 0; -webkit-transition: all 0.3s ease-in-out; -o-transition: all 0.3s ease-in-out; -ms-transition: all 0.3s ease-in-out; -webkit-transition: all 0.3s ease-in-out; }
.feature-effect-box:hover { top: -10px }
.feature-effect-box .feature-i { margin-right: 10px;
width: 50px;
padding: 8px 13px;
padding-bottom: 6px;
border-radius: 50%;
display: inline-block;}
.feature-effect-box .feature-i i{ font-size: 25px;}
.feature-effect-box .feature-icon { display: inline-block; }
.title-box { margin-bottom: 30px;}

body {
background-color: #f9f9fa
}

.flex {
-webkit-box-flex: 1;
-ms-flex: 1 1 auto;
flex: 1 1 auto
}

@media (max-width:991.98px) {
.padding {
padding: 1.5rem
}
}

@media (max-width:767.98px) {
.padding {
padding: 1rem
}
}



.project-card {
background: #fff;
border-width: 0;
border-radius: 20px;
box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
margin-bottom: 1.5rem
}

.project-card {
position: relative;
display: flex;
flex-direction: column;
min-width: 0;
width: 100%;
height: 500px;
word-wrap: break-word;
background-color: #fff;
background-clip: border-box;
border: 1px solid rgba(19, 24, 44, .125);
border-radius: .25rem
}

.project-card-header {
padding: .75rem 1.25rem;
margin-bottom: 0;
background-color: rgba(19, 24, 44, .03);
border-bottom: 1px solid rgba(19, 24, 44, .125)
}

.project-card-header:first-child {
border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
}

.project-card-footer,
.project-card-header {
background-color: transparent;
border-color: rgba(160, 175, 185, .15);
background-clip: padding-box
}
#contextMenu .item {
cursor: pointer;
transition: 1s;
}
#contextMenu .item:hover{
background: #fff5f4;
transition: 1s;
}

.clearfix:after {
clear: both;
}

.clearfix:before,
.clearfix:after {
display: table;
content: " ";
}

.panel {
margin-bottom: 10px;
background-color: #fff;
border: 1px solid transparent;
border-radius: 4px;
-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
}

.panel-footer {
padding: 10px 15px;
background-color: #f5f5f5;
border-top: 1px solid #ddd;
border-bottom-right-radius: 3px;
border-bottom-left-radius: 3px;
}

.panel-heading {
height: 100px;
background-color: turquoise;
padding: 10px 15px;
border-bottom: 1px solid transparent;
border-top-left-radius: 3px;
border-top-right-radius: 3px;
}

.panel-green {
border: 2px dashed #398439;
}

.panel-green .panel-heading {
background-color: #398439;
}

.green {
color: #398439;
}

.blue {
color: #337ab7;
}

.red {
color: #ce7f7f;
}

.panel-primary {
border: 2px dashed #337ab7;
}

.panel-primary .panel-heading {
background-color: #337ab7;
}

.yellow {
color: #ffcc00;
}

.panel-yellow {
border: 2px dashed #ffcc00;
}

.panel-yellow .panel-heading {
background-color: #ffcc00;
}

.panel-red {
border: 2px dashed #ce7f7f;
}

.panel-red .panel-heading {
background-color: #ce7f7f;
}

.huge {
font-size: 30px;
}

.panel-heading {
color: #fff;
}

.pull-left {
float: left !important;
}

.pull-right {
float: right !important;
}

.text-right {
text-align: right;
}

.under-number {
font-size: 20px;
}
.iq-mr--20{
margin-right: 20px;
}

.iq-card .iq-card-header {
    min-height: 45px !important;
}
</style>
@endsection
@section('main-content')
<div class="row pt-4">
    {{-- <div class="col-lg-9 pr-1">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Accounting Options </h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <div class="hr-section">
                    <section id="features">
                        <div class="container-fluid p-0">
                            <div class="row pr-3">
                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.companies.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-primary" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-primary">
                                                <i class="las la-building"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Companies</h5>
                                            </div>
                                            <div class="feature-i iq-bg-primary pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('companies')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.cost-centres.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-dark" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-dark">
                                                <i class="lab la-sourcetree"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Cost Centre's</h5>
                                            </div>
                                            <div class="feature-i iq-bg-dark pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('cost_centres')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.fiscal-years.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-success" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-success">
                                                <i class="las la-calendar-alt"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Fiscal Years</h5>
                                            </div>
                                            <div class="feature-i iq-bg-success pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('fiscal_years')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row pr-3">
                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.entry-types.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-success" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-success">
                                                <i class="las la-journal-whills"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Entry Types</h5>
                                            </div>
                                            <div class="feature-i iq-bg-success pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('entry_types')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.bank-accounts.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-primary" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-primary">
                                                <i class="las la-money-check-alt"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Bank Accounts</h5>
                                            </div>
                                            <div class="feature-i iq-bg-primary pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('bank_accounts')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.tags.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-dark" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-dark">
                                                <i class="las la-tags"></i>
                                            </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Tags</h5>
                                            </div>
                                            <div class="feature-i iq-bg-dark pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('tags')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row pr-3">
                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.chart-of-accounts.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-dark" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-dark">
                                                <i class="las la-sitemap"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Account Groups</h5>
                                            </div>
                                            <div class="feature-i iq-bg-dark pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('account_groups')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.chart-of-accounts.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-success" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-success">
                                                <i class="las la-money-bill"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Chart of Accounts</h5>
                                            </div>
                                            <div class="feature-i iq-bg-success pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('chart_of_accounts')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                
                                <div class="col-md-4 pr-0">
                                    <a href="{{route('accounting.entries.index')}}">
                                        <div class="feature-effect-box wow fadeInUp bg-primary" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-primary">
                                                <i class="lar la-plus-square"></i>
                                              </div>
                                            <div class="feature-icon">
                                              <h5 class="text-white">Entries</h5>
                                            </div>
                                            <div class="feature-i iq-bg-primary pull-right counter mr-0 text-right" style="border-radius: 25% !important; font-weight: bold;margin-top: 2px">
                                                {{ \DB::table('entries')->count() }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-lg-3 pr-0">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Balance Summery</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($balances)) }}" data-labels="{{ implode(',', array_keys($balances)) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-3 pr-0">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Type Wise Entries (Overall)</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($typeWiseEntries['overall'])) }}" data-labels="{{ implode(',', array_keys($typeWiseEntries['overall'])) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-3 pr-0">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Type Wise Entries (Last 7 Days)</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($typeWiseEntries['last-7-days'])) }}" data-labels="{{ implode(',', array_keys($typeWiseEntries['last-7-days'])) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
            </div>
        </div>
    </div> --}}

    <div class="col-lg-3 pr-0">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Type Wise Entries (This Month)</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($typeWiseEntries['this-month'])) }}" data-labels="{{ implode(',', array_keys($typeWiseEntries['this-month'])) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-3 pl-1">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Type Wise Entries (Last Month)</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($typeWiseEntries['last-month'])) }}" data-labels="{{ implode(',', array_keys($typeWiseEntries['last-7-days'])) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="190"></canvas>
            </div>
        </div>
    </div> --}}

    <div class="col-lg-3">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">
                        Type Wise Entries (Fiscal Year)
                    </h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="charts" data-data="{{ implode(',', array_values($typeWiseEntries['current-fiscal-year'])) }}" data-labels="{{ implode(',', array_keys($typeWiseEntries['current-fiscal-year'])) }}" data-chart="doughnut" data-legend-position="top" data-title-text="" width="200" height="210"></canvas>
            </div>
        </div>
    </div>

    @php
        $chartData = implode(',', array_values(getDateWiseTotalTransactions(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'))));

        if(isset($entryTypes[0])){
            foreach($entryTypes as $key => $entryType){
                $chartData .= '|'.implode(',', array_values(getDateWiseTotalTransactions(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), $entryType->id)));
            }
        }
    @endphp

    <div class="col-lg-12 mb-3">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Last 30 days Transactions ({{ date('d-M-y', strtotime('-30 days')) }} to {{ date('d-M-y') }})</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="bar-charts" id="30-days-transactions" data-data="{{ $chartData }}" data-labels="{{ implode(',', array_values(dateRange(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), 'd-M')))  }}" data-legend-position="top" data-title-text="Total,{{ $entryTypes->pluck('name')->implode(',') }}" width="200" height="65"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mb-3">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Income & Expense ({{ date('F Y') }})</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="bar-charts" id="income-expense" data-data="{{ implode(',', array_values($incomes)) }}|{{ implode(',', array_values($expenses)) }}" data-labels="{{ implode(',', array_values(dateRange(date('Y-m-01'), date('Y-m-t'), 'd-M')))  }}" data-legend-position="top" data-title-text="Income,Expense" width="200" height="65"></canvas>
            </div>
        </div>
    </div>

    {{-- <div class="col-lg-6 mb-3">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Last 30 days Transactions ({{ date('d-M-y', strtotime('-30 days')) }} to {{ date('d-M-y') }})</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                <canvas class="bar-charts" id="30-days-transactions" data-data="{{ implode(',', array_values($overallTransactions)) }}" data-labels="{{ implode(',', array_keys($overallTransactions)) }}" data-legend-position="top" data-title-text="Total Amount of Transactions" width="200" height="100"></canvas>
            </div>
        </div>
    </div>

    @if(isset($entryTypes[0]))
    @foreach($entryTypes as $key => $entryType)
    <div class="col-lg-6 mb-3">
        <div class="iq-card p-3" style="padding-top: 5px !important;">
            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                <div class="iq-header-title">
                    <h4 class="card-title text-primary border-left-heading">Last 30 days {{ $entryType->name }} Transactions ({{ date('d-M-y', strtotime('-30 days')) }} to {{ date('d-M-y') }})</h4>
                </div>
            </div>
            <div class="iq-card-body p-0">
                @php
                    $transactions = getDateWiseTotalTransactions(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'), $entryType->id);
                @endphp
                <canvas class="bar-charts" id="30-days-{{ $entryType->label }}-transactions" data-data="{{ implode(',', array_values($transactions)) }}" data-labels="{{ implode(',', array_keys($transactions)) }}" data-legend-position="top" data-title-text="Total Amount of {{ $entryType->name }} Transactions" width="200" height="100"></canvas>
            </div>
        </div>
    </div>
    @endforeach
    @endif --}}

</div>
@endsection
@include('accounting.backend.pages.scripts')