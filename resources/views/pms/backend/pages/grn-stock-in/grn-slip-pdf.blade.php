<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title}}</title>
        <link href="{{ asset('css/mpdf.css') }}" rel="stylesheet">
        <style>
            @page {
                margin-top: .2in;
                margin-bottom: .2in;
                header: page-header;
                footer: page-footer;
            }
            
            html, body, p  {
                font-size:  10px !important;
                color: #000000;
            }
            table {
                width: 100% !important;
                border-spacing: 0px !important;
                margin-top: 10px !important;
                margin-bottom: 15px !important;
            }
            table caption {
                color: #000000 !important;
            }
            table td {
                padding-top: 1px !important;
                padding-bottom: 1px !important;
                padding-left: 7px !important;
                padding-right: 7px !important;
            }
            .table-non-bordered {
                padding-left: 0px !important;
            }
            .table-bordered {
                border-collapse: collapse;
            }
            .table-bordered td {
                border: 1px solid #000000;
                padding: 5px;
            }

            .table-no-bordered td {
                border: none !important;
                padding: 5px;
            }

            .table-bordered tr:first-child td {
                border-top: 0;
            }
            .table-bordered tr td:first-child {
                border-left: 0;
            }
            .table-bordered tr:last-child td {
                border-bottom: 0;
            }
            .table-bordered tr td:last-child {
                border-right: 0;
            }
            .mt-0 {
                margin-top: 0; 
            }
            .mb-0 {
                margin-bottom: 0; 
            }
            .image-space {
                white-space: wrap !important;
                padding-top: 45px !important;
            }
            .break-before {
                page-break-before: always;
                break-before: always;
            }
            .break-after {
                break-after: always;
            }
            .break-inside {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .break-inside-auto { 
                page-break-inside: auto;
                break-inside: auto;
            }
            .space-top {
                margin-top: 10px;
            }
            .space-bottom {
                margin-bottom: 10px;
            }

            .text-center{
                text-align:  center;
            }
            .text-right{
                text-align:  right;
            }           
        </style>    
    </head>
    
    <body>
        <htmlpageheader name="page-header">
            
        </htmlpageheader>

        <htmlpagefooter name="page-footer">
            
        </htmlpagefooter>
        
        <div class="container">
            <div class="row">
                @if(isset($note->id))
                <table class="table-bordered">
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ccc">
                                <table class="table-no-bordered">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="text-align: center">
                                                <h1><strong>TECHNOCRATS ERP</strong></h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Reference</td>
                                            <td style="width: 80%">
                                                {{$note->grn_reference_no}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Date</td>
                                            <td style="width: 80%">
                                                {{date('d-m-Y',strtotime($note->received_date))}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Challan</td>
                                            <td style="width: 80%">
                                                {{$note->challan}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Supplier</td>
                                            <td style="width: 80%">
                                                {{$note->relPurchaseOrder->relQuotation->relSuppliers->name}}
                                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                                {{$note->relPurchaseOrder->relQuotation->relSuppliers->email}}
                                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                                {{$note->relPurchaseOrder->relQuotation->relSuppliers->phone}}
                                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                                {{$note->relPurchaseOrder->relQuotation->relSuppliers->address}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 50%">
                                                <strong>Product</strong>
                                            </td>
                                            <td style="width: 20%" class="text-center">
                                                <strong>Warhouse</strong>
                                            </td>
                                            <td style="width: 15%" class="text-center">
                                                <strong>Unit</strong>
                                            </td>
                                            <td style="width: 15%" class="text-right">
                                                <strong>Qty</strong>
                                            </td>
                                        </tr>
                                        @if($items->count() > 0)
                                        @foreach($items as $key=>$item)
                                        <tr>
                                            <td>
                                                {{$item->relProduct->name}} ({{ getProductAttributes($item->product_id) }})
                                            </td>
                                            <td class="text-center">
                                                {{isset($item->relGoodsReceivedItemStockIn[0]->relWarehouse->name) ? $item->relGoodsReceivedItemStockIn[0]->relWarehouse->name : ''}}
                                            </td>
                                            <td class="text-center">
                                                {{$item->relProduct->productUnit->unit_name}}
                                            </td>
                                            <td class="text-right">
                                                {{number_format($item->qty,0)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td class="text-right"><strong>{{ number_format($items->sum('qty'), 0) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h6 class="text-center" >Thank you for doing business with us!</h6>
                                <br>
                                <center>
                                    <img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($note->grn_reference_no, 'C39',1,33)!!}" alt="barcode"/>
                                </center>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </body>
</html>                                                                                                                                                                                                                             