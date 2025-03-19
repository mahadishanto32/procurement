@php
$corporateAddress = \App\Models\PmsModels\SupplierAddress::where(['supplier_id' => isset($supplier->id) ? $supplier->id : 0, 'type' => 'corporate'])->first();
$contactPersonSales = \App\Models\PmsModels\SupplierContactPerson::where(['supplier_id' => isset($supplier->id) ? $supplier->id : 0, 'type' => 'sales'])->first();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Request For Proposal Mail</title>
    <style type="text/css">
        .invoiceBody{
            margin-top:10px;
            background:#eee;
            padding: 20px;
            padding-left: 30px;
        }
        
        th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        tbody {
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            border: 1px solid #dee2e6;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        strong {
            font-weight: bolder;
        }

        .list-unstyled {padding-left: 0;list-style: none;
        }

        .main-body {
            page-break-after: always;
        }
        
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div id="app">

        <div style="max-width: 1190px;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;">
            <main class="" style="padding-bottom: 0;">
                <div id="main-body">
                    <div class="main-content">
                        <div class="main-content-inner">
                            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                            </div>
                            @if(isset($requestProposal))
                            <div style="display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">

                                
                            <div style="flex: 0 0 100%;max-width: 100%; padding-top:30px" id="print_invoice">

                                <div class="panel panel-body">

                                    <div style="max-width: 100%;">
                                        <div class="invoice-details" style="margin-top:25px;display: flex;flex-wrap: wrap;">
                                        <table  style="width: 100%;margin-bottom: 1rem;color: #212529;border: 1px solid #000;border-collapse: collapse;box-sizing: border-box;isplay: table;border-collapse: separate;box-sizing: border-box;text-indent: initial;border-color: grey;" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 50% !important">
                                                        <strong>Vendor Name:&nbsp;{{isset($supplier->name) ? $supplier->name : ''}}</strong>
                                                    </td>
                                                    <td style="width: 50% !important;text-align: right !important">
                                                        <img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($requestProposal->reference_no, 'C39',1,33)!!}" alt="barcode" style="float: right" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50% !important;font-size: 14px !important;">
                                                        Address:&nbsp;
                                                        @if(isset($corporateAddress->id))
                                                        {{ $corporateAddress->road.' '.$corporateAddress->village.', '.$corporateAddress->city.'-'.$corporateAddress->zip.', '.$corporateAddress->country }}
                                                        <br>
                                                        {{ $corporateAddress->adddress }}
                                                        @endif
                                                    </td>
                                                    <td style="width: 50% !important;font-size: 14px !important;text-align: right">
                                                        Date:&nbsp;{{ date('jS F Y', strtotime($requestProposal->request_date)) }}
                                                        <br>
                                                        Reference No:&nbsp;{{ $requestProposal->reference_no }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 100% !important;font-size: 14px !important;" colspan="2">
                                                        Attention:&nbsp;
                                                        @if(isset($contactPersonSales->id))
                                                        {{ $contactPersonSales->name.', '.$contactPersonSales->designation }},
                                                        Mobile:&nbsp;{{ $contactPersonSales->mobile }},
                                                        Mail:&nbsp;{{ $contactPersonSales->email }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div style="display: block;width: 100%;overflow-x: auto;">

                                    <table style="width: 100%;margin-bottom: 1rem;color: #212529;border: 1px solid #000;border-collapse: collapse;box-sizing: border-box;isplay: table;border-collapse: separate;box-sizing: border-box;text-indent: initial;border-color: grey;" cellspacing="0" cellpadding="0" border="1">
                                        <thead style="display: table-header-group;vertical-align: middle;border-color: inherit;">
                                            <tr style="display: table-row;vertical-align: inherit;">
                                                <th style="padding: 10px 5px">Sl No.</th>
                                                <th style="padding: 10px 5px">Product</th>
                                                <th style="padding: 10px 5px">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($requestProposal->requestProposalDetails as $key=>$value)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td style="text-align:center">{{$value->product->name}}</td>
                                                <td style="text-align:center">{{$value->request_qty}}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="item">
                                                <th style="padding: 10px 5px" colspan="2" align="right">Total QTY :</th>
                                                <th style="padding: 10px 5px">{{$requestProposal->requestProposalDetails->sum('request_qty')}}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                    <div class="form-group">
                                        <label for="terms-condition"><strong>Terms & Conditions</strong>:</label>
                                        <div style="padding-left: 20px">{!! isset($supplier->term_condition) ? $supplier->term_condition : '' !!}</div>
                                    </div>

                                    <div class="form-group">
                                        <small>(Note: This Request for Proposal doesn’t require signature as it is automatically generated from MBM Group’s ERP)</small>
                                    </div>
                            </div>
                        </div>

                    </div>
                    @endif

                </div>
            </div>
        </div>
    </main>
</div>
</div>
</body>
</html>
