<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{{$title}}</title>
</head>
<body>
	<p style="font-size:16px;font-weight:500">Hi {{ $purchaseOrder->relQuotation->relSuppliers->name}},</p>
	<p style="font-size:16px;font-weight:500">You receive an order against this quotation number ({{$purchaseOrder->relQuotation->reference_no}}) from MBM group, PO reference is  {{$purchaseOrder->reference_no}}.</p>
	<p style="font-size:16px;font-weight:500">Please check the attachment below and reply to us if you need to know more.</p>

	<p style="font-size:16px;font-weight:500">Yours truly,</p>
	<p style="font-size:14px">MBM Garments Limited.</p>
	<p style="margin-top:10px"></p>
</body>
</html>