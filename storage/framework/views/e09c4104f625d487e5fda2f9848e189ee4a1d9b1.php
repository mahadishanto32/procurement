
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="<?php echo e(route('pms.dashboard')); ?>"><?php echo e(__('Home')); ?></a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active"><?php echo e(__($title)); ?></li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form action="<?php echo e(url('pms/grn/po-list')); ?>" method="get" accept-charset="utf-8">
                        <div class="row pl-4 pt-3">
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(request()->has('from_date') ? date("d-m-Y", strtotime(request()->get('from_date'))) : date("d-m-Y", strtotime(date('Y-m-01')))); ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(request()->has('to_date') ? date("d-m-Y", strtotime(request()->get('to_date'))) : date("d-m-Y", strtotime(date('Y-m-d')))); ?>">
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchPOList"></label></p>
                                <div class="input-group input-group-md">
                                    <button type="submit" class="btn btn-success rounded mt-8"><i class="las la-search"></i>Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel panel-body">

                    <div class="table-responsive" id="viewResult">
                        <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('Unit')); ?></th>
                                    <th><?php echo e(__('PO.Date')); ?></th>
                                    <th><?php echo e(__('Supplier')); ?></th>
                                    <th><?php echo e(__('Reference No')); ?></th>
                                    <th><?php echo e(__('Quotation Ref No')); ?></th>
                                    <th><?php echo e(__('P.O Qty')); ?></th>
                                    <th><?php echo e(__('Gate-In Qty')); ?></th>
                                    <th><?php echo e(__('Total Price')); ?></th>
                                    <th><?php echo e(__('Discount')); ?></th>
                                    <th><?php echo e(__('Vat')); ?></th>
                                    <th><?php echo e(__('Gross Price')); ?></th>
                                    <th><?php echo e(__('BarCode')); ?></th>
                                    <th class="text-center" style="width: 20%"><?php echo e(__('Option')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data)>0): ?>
                                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(($data->currentpage()-1) * $data->perpage() + $key + 1); ?></td>
                                    <td><?php echo e($values->Unit->hr_unit_short_name); ?></td>
                                    <td><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
                                    <td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>
                                    <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>
                                    <td><?php echo e($values->relQuotation?$values->relQuotation->reference_no:''); ?></td>

                                    <td><?php echo e($values->relPurchaseOrderItems->sum('qty')); ?></td>
                                    <td><?php echo e($values->total_grn_qty); ?></td>

                                    <td><?php echo e($values->total_price); ?></td>
                                    <td><?php echo e($values->discount); ?></td>
                                    <td><?php echo e($values->vat); ?></td>
                                    <td><?php echo e($values->gross_price); ?></td>
                                    <td>    
                                    
                                    
                                    <img src="data:image/png;base64,<?php echo DNS1D::getBarcodePNG($values->reference_no, 'C39',1,33); ?>" alt="barcode" />
                                    </td>

                                    <td class="text-center">
                                        <?php if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty): ?>
                                            <a class="btn btn-success btn-xs mb-1"><?php echo e(__('Full Received')); ?></a>
                                        <?php elseif($values->relPurchaseOrderItems->sum('qty')>$values->total_grn_qty): ?>

                                            <?php if($values->total_grn_qty > 0): ?>
                                            <a class="btn btn-warning btn-xs mb-1"><?php echo e(__('Partially Received')); ?></a>
                                            <br>
                                            <?php endif; ?>
                                            
                                            <a href="<?php echo e(route('pms.grn.grn-list.createGRN',$values->id)); ?>" data-toggle="tooltip" title="Click here to generate Gate-In">
                                                <button type="button" class="btn btn-xs btn-primary mb-1"><?php echo e(__('Gate-In')); ?></button>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('pms.grn.grn-list.createGRN',$values->id)); ?>" data-toggle="tooltip" title="Click here to generate Gate-In">
                                                <button type="button" class="btn btn-sm btn-primary"><?php echo e(__('Gate-In')); ?></button>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($values->total_grn_qty > 0): ?>
                                        <br>
                                        <a class="btn btn-primary btn-xs" href="<?php echo e(url('pms/grn/gate-in-slip/'.$values->id)); ?>" target="_blank"><i class="la la-print"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="col-12 py-2">
                            <?php if(count($data)>0): ?>
                            <ul>
                                <?php echo e($data->links()); ?>

                            </ul>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>

<script>
    (function ($) {
        "use script";

        $('#searchPOList').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();

            const searchPOList = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        showPreloader('block');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchPOList();
                                     showPODetails();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
            };
            
            if (from_date !='' || to_date !='') {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date},
                    success:function (data) {
                        if(data.result == 'success'){
                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchPOList();
                             showPODetails();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');
            }
        });


        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showPODetails();

    })(jQuery);
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn/po-index.blade.php ENDPATH**/ ?>