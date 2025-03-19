<?php
use \App\Models\PmsModels\Requisition;
use \App\Models\PmsModels\RequisitionTracking;
use \App\Models\PmsModels\RequisitionDeliveryItem;
use \App\Models\PmsModels\Notification;

use \App\Models\MyProject\Project;

use \App\Models\PmsModels\Purchase\PurchaseOrder;
use \App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
use \App\Models\PmsModels\Grn\GoodsReceivedItemStockIn;

use \App\Models\PmsModels\Suppliers;
use \App\Models\PmsModels\SupplierPayment;

use \App\Models\PmsModels\Rfp\RequestProposal;
use \App\Models\PmsModels\Quotations;

use \App\Models\PmsModels\Accounts\AccountGroup;
use \App\Models\PmsModels\Accounts\EntryType;
use \App\Models\PmsModels\Accounts\Entry;
use \App\Models\PmsModels\Accounts\EntryItem;

function userData(){
    $tracks = RequisitionTracking::when(isset(auth()->user()->employee->as_department_id),function($query){
        return $query->whereHas('requisition.relUsersList.employee',function($query){
            return $query->where('as_department_id',auth()->user()->employee->as_department_id);
        });
    })
    ->whereHas('requisition', function($query){
        return $query->where('author_id', auth()->user()->id);
    })->get();
    

    $notifications = Notification::when(isset(auth()->user()->employee->as_department_id),function($query){
            return $query->whereHas('relUser.employee',function($query){
                return $query->where('as_department_id',auth()->user()->employee->as_department_id);
            });
        })
        ->where('user_id',auth()->user()->id)
        ->get();

    $deliveredRequisitions = RequisitionDeliveryItem::when(isset(auth()->user()->employee->as_department_id),function($query){
        return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
            return $query->where('as_department_id',auth()->user()->employee->as_department_id);
        });
    })
    ->whereHas('relRequisitionDelivery.relRequisition',function($query){
        return $query->where('author_id',auth()->user()->id);
    })
    ->get();

    //Users requistion on department head
    $userRequisitionData = Requisition::when(isset(Auth::user()->employee->as_department_id),
            function($query){
                return $query->whereHas('relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
        })
        ->get();

    return [
        'requisitions' => [
            'draft' => collect($tracks)->where('status', 'draft')->count(),
            'pending' => collect($tracks)->where('status', 'pending')->count(),
            'approved' => collect($tracks)->where('status', 'approved')->count(),
            'processing' => collect($tracks)->where('status', 'processing')->count(),
            'delivered' => collect($tracks)->where('status', 'delivered')->count(),
            'received' => collect($tracks)->where('status', 'received')->count(),
        ],
        'notifications' => [
            'read' => collect($notifications)->where('type','read')->count(),
            'unread' => collect($notifications)->where('type','unread')->count(),
        ],
        'delivered-requisitions' => [
            'pending' => collect($deliveredRequisitions)->where('status', 'pending')->count(),
            'acknowledge' => collect($deliveredRequisitions)->where('status', 'acknowledge')->count(),
            'delivered' => collect($deliveredRequisitions)->where('status', 'delivered')->count(),
        ],
        'user-requisitions' => [
            'pending' => collect($userRequisitionData)->where('status', 0)->count(),
            'acknowledge' => collect($userRequisitionData)->where('status', 1)->count(),
            'halt' => collect($userRequisitionData)->where('status', 2)->count(),
        ],
    ];
}

function projectData(){
    return [
        'names' => \App\Models\MyProject\Project::where("status", "approved")->pluck('name')->toArray(),
        'progresses' => \App\Models\MyProject\Project::where("status", "approved")->pluck('status_at')->toArray(),
    ];
}

function storeData(){
    return [
        'store-manage' => [
            'requistions' => Requisition::with('relUsersList')
            ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->count(),

            'rfp-requistions' => Requisition::where(function($query){
                return $query->where(['status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->orWhere('request_status','rfp');
            })
            ->count(),

            'pending-delivery' => Requisition::where(['status'=>1,'delivery_status'=>'partial-delivered'])->count(),

            'complete-delivery' => Requisition::where(['status'=>1,'delivery_status'=>'delivered'])->count(),
        ],
        'grn' => [
            'qce-list' => PurchaseOrder::with('relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
                ->where('is_send','yes')
                ->whereHas('relGoodReceiveNote', function ($query){
                    $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
                })
                ->whereHas('relGoodReceiveNote.relGoodsReceivedItems', function($query){
                    return $query->whereIn('id', GoodsReceivedItemStockIn::where('is_grn_complete','no')->pluck('goods_received_item_id')->all());
                })
                ->count(),

            'grn-list' => PurchaseOrder::with('relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')
                ->where('is_send','yes')
                ->whereHas('relGoodReceiveNote', function ($query){
                    $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
                })
                ->whereHas('relGoodReceiveNote.relGoodsReceivedItems', function($query){
                    return $query->whereIn('id', GoodsReceivedItemStockIn::where('is_grn_complete','yes')->pluck('goods_received_item_id')->all());
                })
                ->count(),
        ]
    ];
}

function topSuppliers($howMany){
    return Suppliers::addSelect(['pay_amount' => SupplierPayment::selectRaw('sum(pay_amount) as total_pay_amount')
         ->whereColumn('supplier_id', 'suppliers.id')
         ->groupBy('supplier_id')
     ])
    ->has('relQuotations.relPurchaseOrder')
     ->orderBy('pay_amount', 'DESC')
     ->take($howMany)
     ->get();
}

function purchaseStats(){
    return [
        'rfp-requistions' => Requisition::where(function($query){
            return $query->where(['status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->orWhere('request_status','rfp');
        })
        ->count(),
        
        'proposals' => RequestProposal::with('relQuotations')->count(),

        'quotations' => Quotations::where(['status'=>'active', 'is_approved'=>'approved', 'is_po_generate'=>'yes'])->count(),

        'purchase-orders' => PurchaseOrder::count(),
    ];
}

function dateRange($from, $to, $format = "Y-m-d"){
    $range = [];
    if(strtotime($from) && strtotime($to)){
        $begin = new \DateTime($from);
        $end = new \DateTime($to);
        
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($begin, $interval, $end);
        
        
        foreach ($dateRange as $date) {
            $range[] = $date->format($format);
        }
        array_push($range, date($format,strtotime($to)));
    }
    
    return $range;
}

function billingData($from, $to){
    $dateRange = dateRange($from, $to);
    $purchaseOrders = PurchaseOrder::where('is_send','yes')
    ->whereHas('relGoodReceiveNote', function ($query){
        $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
    })
    ->whereIn(\DB::raw('substr(`po_date`, 1, 10)'), $dateRange)
    ->pluck('id')->toArray();

    $data = [];
    if(isset($dateRange[0])){
        foreach ($dateRange as $date) {
            $data['po'][date('d-M', strtotime($date))] = PurchaseOrder::whereIn('id', $purchaseOrders)
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->sum('gross_price');

            $data['grn'][date('d-M', strtotime($date))] = GoodsReceivedItemStockIn::whereIn('purchase_order_id', $purchaseOrders)
            ->where('is_grn_complete','yes')
            ->where(\DB::raw('substr(`created_at`, 1, 10)'), $date)
            ->sum('total_amount');

            $data['bill'][date('d-M', strtotime($date))] = PurchaseOrderAttachment::whereIn('purchase_order_id', $purchaseOrders)
            ->where(\DB::raw('substr(`created_at`, 1, 10)'), $date)
            ->sum('bill_amount');
        }
    }
    return $data;
}

function auditStat()
{
    $purchaseOrders = \App\Models\PmsModels\Purchase\PurchaseOrder::where('is_send','yes')
    ->whereHas('relGoodReceiveNote', function ($query){
        $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
    })->get();

    $pending = \App\Models\PmsModels\Purchase\PurchaseOrder::where('is_send','yes')
    ->whereHas('relGoodReceiveNote', function ($query){
        $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
    })->whereHas('relPoAttachment', function ($query){
               $query->where('bill_type','po')->where('status','pending');
            })->count();

    $approved = \App\Models\PmsModels\Purchase\PurchaseOrder::where('is_send','yes')
    ->whereHas('relGoodReceiveNote', function ($query){
        $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
    })->whereHas('relPoAttachment', function ($query){
               $query->where('bill_type','po')->where('status','approved');
            })->count();

    
     return [
        
        'purchase-orders' => [
            'total-bill' => collect($purchaseOrders)->count(),
            'pending-bill' => $pending ,
            'approved-bill' =>  $approved,
        ],
        
    ];
}

function typeWiseEntries($from = false, $to = false){
    $types = EntryType::all();
    $data = [];
    if(isset($types[0])){
        foreach ($types as $key => $type) {
            $data[$type->name] = Entry::where('entry_type_id', $type->id)
            ->when($from, function($query) use($from){
                return $query->where('date', '>=', $from);
            })
            ->when($to, function($query) use($to){
                return $query->where('date', '<=', $to);
            })
            ->sum('debit');
        }
    }
    return $data;
}

function getDateWiseTotalTransactions($from, $to, $entry_type_id = 0){
    $dateRange = dateRange($from, $to);
    $data = [];
    if(isset($dateRange[0])){
        foreach ($dateRange as $key => $date) {
            $data[date('d-M', strtotime($date))] = Entry::where('date', $date)
            ->when($entry_type_id > 0, function($query) use($entry_type_id){
                return $query->where('entry_type_id', $entry_type_id);
            })
            ->sum('debit');
        }
    }
    return $data;
}

function groupWiseBalance($from, $to, $group_id = 0){
    $dateRange = dateRange($from ? $from : date('Y-m-01'), $to ? $to : date('Y-m-t'));
    $data = [];
    if(isset($dateRange[0])){
        foreach ($dateRange as $key => $date) {
            $items = EntryItem::whereHas('entry', function($query) use($date){
                return $query->where('date', $date);
            })
            ->whereIn('chart_of_account_id', call_user_func_array('array_merge', getAllAccounts($group_id)));

            $data[date('d-M', strtotime($date))] = ($items->where('debit_credit', 'D')->sum('amount')-$items->where('debit_credit', 'C')->sum('amount'));
        }
    }
    return $data;
}

function balances(){
    $groups = AccountGroup::where('parent_id', 0)->get();
    $balances = [];
    if(isset($groups[0])){
        foreach($groups as $key => $group){
            $items = EntryItem::whereIn('chart_of_account_id', call_user_func_array('array_merge', getAllAccounts($group->id)));
            $balances[$group->name] = ($items->where('debit_credit', 'D')->sum('amount')-$items->where('debit_credit', 'C')->sum('amount'));
        }
    }
    return $balances;
}

function gateManagerData($from = false, $to = false){
    $dateRange = dateRange($from ? $from : date('Y-m-d', strtotime('-1 months')), $to ? $to : date('Y-m-d'));
    $poData = [];
    $gateInData = [];

    if(isset($dateRange[0])){
        foreach ($dateRange as $key => $date) {
            $poData[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)->count();
            $gateInData[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->count();
        }
    }
    
    return [
        'po' => $poData,
        'gate-in' => $gateInData
    ];
}

function gateQualityControllerData($from = false, $to = false){
    $dateRange = dateRange($from ? $from : date('Y-m-d', strtotime('-30 days')), $to ? $to : date('Y-m-d'));
    $gateIn = [];
    $approved = [];
    $returned = [];
    $returnedChanged = [];

    if(isset($dateRange[0])){
        foreach ($dateRange as $key => $date) {
            $gateIn[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->count();

            $approved[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure', 'approved');
            })
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->count();

            $returned[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure', 'return');
            })
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->count();

            $returnedChanged[date('d-M', strtotime($date))] = PurchaseOrder::where('is_send','yes')
            ->whereHas('relGoodReceiveNote.relGoodsReceivedItems',function ($query){
                return $query->where('quality_ensure', 'return-change');
            })
            ->where(\DB::raw('substr(`po_date`, 1, 10)'), $date)
            ->count();
        }
    }
    
    return [
        'gate-in' => $gateIn,
        'approved' => $approved,
        'returned' => $returned,
        'return-changed' => $returnedChanged,
    ];
}

function managementData($from = false, $to = false){
    $dateRange = dateRange($from ? $from : date('Y-m-d', strtotime('-30 days')), $to ? $to : date('Y-m-d'));
    $total = [];
    $processing = [];
    $approved = [];
    $halt = [];

    if(isset($dateRange[0])){
        foreach ($dateRange as $key => $date) {
            $processing[date('d-M', strtotime($date))] = Quotations::where([
                'status' => 'active',
                'is_po_generate' => 'no'
            ])
            ->where('is_approved', 'processing')
            ->where(\DB::raw('substr(`quotation_date`, 1, 10)'), $date)
            ->groupBy('request_proposal_id')
            ->count();

            $approved[date('d-M', strtotime($date))] = Quotations::where([
                'status' => 'active',
                'is_po_generate' => 'no'
            ])
            ->where('is_approved', 'approved')
            ->where(\DB::raw('substr(`quotation_date`, 1, 10)'), $date)
            ->groupBy('request_proposal_id')
            ->count();

            $halt[date('d-M', strtotime($date))] = Quotations::where([
                'status' => 'active',
                'is_po_generate' => 'no'
            ])
            ->where('is_approved', 'halt')
            ->where(\DB::raw('substr(`quotation_date`, 1, 10)'), $date)
            ->groupBy('request_proposal_id')
            ->count();

            $total[date('d-M', strtotime($date))] = Quotations::where([
                'status' => 'active',
                'is_po_generate' => 'no'
            ])
            ->whereIn('is_approved', ['processing', 'approved', 'halt'])
            ->where(\DB::raw('substr(`quotation_date`, 1, 10)'), $date)
            ->groupBy('request_proposal_id')
            ->count();
        }
    }
    
    return [
        'total' => $total,
        'processing' => $processing,
        'approved' => $approved,
        'halt' => $halt,
    ];
}