<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ $title }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="row pr-3 pl-2">
            <table class="table">
                <tbody>
                    <tr>
                        <td style="border-top: none !important">Code:</td>
                        <td style="border-top: none !important">Number:</td>
                        <td style="border-top: none !important">Date:</td>
                        <td style="border-top: none !important">Tag:</td>
                        <td style="border-top: none !important">Fiscal Year:</td>
                    </tr>
                    <tr>
                        <td style="border-top: none !important">
                            <strong>{{ $entry->code }}</strong>
                        </td>
                        <td style="border-top: none !important">
                            <strong>{{ $entry->number }}</strong>
                        </td>
                        <td style="border-top: none !important">
                            <strong>{{ $entry->date }}</strong>
                        </td>
                        <td style="border-top: none !important">
                            <strong>{{ $entry->tag ? $entry->tag->title : '' }}</strong>
                        </td>
                        <td style="border-top: none !important">
                            <strong>{{ $entry->fiscalYear->title }}&nbsp;|&nbsp;{{ date('d-M-y', strtotime($entry->fiscalYear->start)).' to '.date('d-M-y', strtotime($entry->fiscalYear->end)) }})</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10%">D/C</th>
                            <th style="width: 15%">Cost Centre</th>
                            <th style="width: 25%">Ledger</th>
                            <th style="width: 15%">Debit</th>
                            <th style="width: 15%">Credit</th>
                            <th style="width: 20%">Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($entry->items->count() > 0)
                        @foreach($entry->items as $key => $item)
                        <tr>
                            <td>{{ $item->debit_credit == "D" ? "Debit" : "Credit" }}</td>
                            <td>{{ $item->costCentre ? '['.$item->costCentre->code.'] '.$item->costCentre->name : '' }}</td>
                            <td>{{ $item->chartOfAccount ? '['.$item->chartOfAccount->code.'] '.$item->chartOfAccount->name : '' }}</td>
                            <td class="text-right">{{ $item->debit_credit == "D" ? $item->amount : '' }}</td>
                            <td class="text-right">{{ $item->debit_credit == "C" ? $item->amount : '' }}</td>
                            <td>{{ $item->narration }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                    @php
                        $total_debit = $entry->items->where('debit_credit', 'D')->sum('amount');
                        $total_credit = $entry->items->where('debit_credit', 'C')->sum('amount');
                        $d_deference = $total_credit > $total_debit ? ($total_credit-$total_debit) : 0;
                        $c_deference = $total_debit > $total_credit ? ($total_debit-$total_credit) : 0;
                    @endphp
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <h5><strong>Total</strong></h5>
                            </td>
                            <td style="font-weight: bold;" class="text-right total-debit {{ $d_deference > 0 || $c_deference > 0 ? 'bg-danger' : 'bg-success' }}">
                                {{ $total_debit }}
                            </td>
                            <td style="font-weight: bold;" class="text-right total-credit {{ $d_deference > 0 || $c_deference > 0 ? 'bg-danger' : 'bg-success' }}">
                                {{ $total_credit }}
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <h5><strong>Difference</strong></h5>
                            </td>
                            <td style="font-weight: bold;" class="text-right debit-difference">
                                {{ $d_deference > 0 ? $d_deference : '' }}
                            </td>
                            <td style="font-weight: bold;" class="text-right credit-difference">
                                {{ $c_deference > 0 ? $c_deference : '' }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <table>
                    <tbody>
                        <tr>
                            <td><strong>Notes:</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <p>{{ $entry->notes }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    </div>
  </div>
</body>
</html>