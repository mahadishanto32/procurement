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
      <table style="width: 100% !important" class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="2" class="pt-3 pb-3">
                    <h5>Ledger statement for <strong>[{{ $account->code }}] {{ $account->name }}</strong> from <strong>{{ date('d-M-Y', strtotime($from)) }}</strong> to <strong>{{ date('d-M-Y', strtotime($to)) }}</strong></h5>
                </td>
            </tr>
            <tr>
                <td style="width: 50%" class="pr-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 50% !important">Bank or cash account</td>
                                <td style="width: 50% !important">{{ $account->bank_or_cash == 1 ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td style="width: 50% !important">Notes</td>
                                <td style="width: 50% !important">{{ $account->notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 50%" class="pl-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 50% !important">Opening balance as on <strong>{{ date('d-M-Y', strtotime($from)) }}</strong></td>
                                <td style="width: 50% !important">{{ $openingBalance }}</td>
                            </tr>
                            <tr>
                                <td style="width: 50% !important">Closing balance as on <strong>{{ date('d-M-Y', strtotime($to)) }}</strong></td>
                                <td style="width: 50% !important">{{ $closingBalance }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                               <th style="width: 10%">{{__('Date')}}</th>
                               <th style="width: 10%">{{__('Number')}}</th>
                               <th style="width: 25%">{{__('Ledger')}}</th>
                               <th style="width: 10%">{{__('Type')}}</th>
                               <th style="width: 15%" class="text-right">{{__('Debit')}}</th>
                               <th style="width: 15%" class="text-right">{{__('Credit')}}</th>
                               <th style="width: 15%" class="text-right">{{__('Balance')}}</th>
                           </tr>
                       </thead>
                       <tbody>
                        <tr>
                            <td colspan="6">Current opening balance</td>
                            <td class="text-right">{{ $openingBalance }}</td>
                        </tr>
                        @if(isset($entries[0]))
                        @foreach($entries as $key => $entry)
                        @php 
                            $balance = ($openingBalance+($entry->debit-$entry->credit));  
                        @endphp
                        <tr>
                            <td>{{ $entry->date }}</td>
                            <td>{{ $entry->number }}</td>
                            <td>
                                <p>Debit: {{ $entry->items->where('debit_credit', 'D')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                                <p>Credit: {{ $entry->items->where('debit_credit', 'C')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                            </td>
                            <td>{{ $entry->entryType ? $entry->entryType->name : '' }}</td>
                            <td class="text-right">{{ $entry->debit }}</td>
                            <td class="text-right">{{ $entry->credit }}</td>
                            <td class="text-right">{{ $balance }}</td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <td colspan="6">Current closing  balance</td>
                            <td class="text-right">{{ $closingBalance }}</td>
                        </tr>
                       </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
  </div>
</body>
</html>