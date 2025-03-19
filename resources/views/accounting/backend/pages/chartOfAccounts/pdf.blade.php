<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ $title }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <style type="text/css">
    tr > td:last-of-type {
	    display: none !important;
	}
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <table class="table table-head" cellspacing="0" width="100%" id="dataTable">
        <thead>
            <tr>
               <th style="width: 25%">{{__('Account Code')}}</th>
               <th style="width: 40%">{{__('Account Name')}}</th>
               <th class="text-center" style="width: 15%">{{__('Type')}}</th>
               <th class="text-right" style="width: 20%">{{__('Opening Balance')}}</th>
           </tr>
       </thead>
       <tbody>
        {!! $accountGroups !!}
       </tbody>
     </table>
  	</div>
  </div>
</body>
</html>