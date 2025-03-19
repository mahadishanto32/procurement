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
      <table class="table table-bordered" cellspacing="0" width="100%" id="dataTable">
        <thead>
            <tr>
               <th style="width: 10%">{{__('Date')}}</th>
               <th style="width: 10%">{{__('Number')}}</th>
               <th style="width: 30%">{{__('Ledger')}}</th>
               <th style="width: 10%">{{__('Type')}}</th>
               <th style="width: 10%">{{__('Tag')}}</th>
               <th style="width: 10%">{{__('Debit')}}</th>
               <th style="width: 10%">{{__('Credit')}}</th>
           </tr>
       </thead>
       <tbody>
        @if(isset($entries[0]))
        @foreach($entries as $key => $entry)
        <tr>
            <td>{{ $entry->date }}</td>
            <td>{{ $entry->number }}</td>
            <td>
                <p>Debit: {{ $entry->items->where('debit_credit', 'D')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                <p>Credit: {{ $entry->items->where('debit_credit', 'C')->pluck('chartOfAccount.code')->implode(', ') }}</p>
            </td>
            <td>{{ $entry->entryType ? $entry->entryType->name : '' }}</td>
            <td>{{ $entry->tag ? $entry->tag->title : '' }}</td>
            <td class="text-right">{{ $entry->debit }}</td>
            <td class="text-right">{{ $entry->credit }}</td>
        </tr>
        @endforeach
        @endif
       </tbody>
    </table>
  	</div>
  </div>
</body>
</html>