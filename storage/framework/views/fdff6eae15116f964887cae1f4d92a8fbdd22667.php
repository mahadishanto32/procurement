
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
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
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="to_date" id="to_date" class="form-control search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>" readonly>
                                </div>
                            </div>

                            
                            <input type="hidden" name="is_approved" id="is_approved" value="approved">
                            
                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchQuotationBtn"></label></p>
                                <div class="input-group input-group-md">
                                    <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.quotation.approved.view.search')); ?>" id="searchQuotationBtn"> <i class="las la-search"></i>Search</a>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div id="dataTableView">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('Date')); ?></th>

                                    <th><?php echo e(__('Request Proposal')); ?></th>
                                    <th><?php echo e(__('Quotation')); ?></th>
                                    <th><?php echo e(__('Qty')); ?></th>
                                    <th><?php echo e(__('Unit Price')); ?></th>
                                    <th><?php echo e(__('Total Amount')); ?></th>
                                    <th><?php echo e(__('Supplier')); ?></th>
                                    <th class="text-center"><?php echo e(__('Option')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($quotationList)>0): ?>
                                <?php $__currentLoopData = $quotationList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                $total_qty=0;
                                $total_unit_price=0;
                                $total_amount=0;

                                foreach($values->relSelfQuotationSupplierByProposalId()->where('is_approved','approved')->get() as $supplier){

                                    $total_qty +=$supplier->relQuotationItems->sum('qty');
                                    $total_unit_price +=$supplier->relQuotationItems->sum('unit_price');
                                    $total_amount +=$supplier->relQuotationItems->sum('total_price');
                                }
                            ?>
                            <tr id="removeRow<?php echo e($values->id); ?>">
                                <td><?php echo e(($quotationList->currentpage()-1) * $quotationList->perpage() + $key + 1); ?></td>
                                <td><?php echo e(date('d-m-Y', strtotime($values->quotation_date))); ?></td>

                                <td><a href="javascript:void(0)" class="btn btn-link" onclick="requestProposalDetails(<?php echo e($values->relRequestProposal->id); ?>)"><?php echo e($values->relRequestProposal->reference_no); ?></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)"  class="btn btn-link"><?php echo e($values->reference_no); ?></a>
                                </td>
                                <td><?php echo e(number_format($total_qty,2)); ?></td>
                                <td><?php echo e(number_format($total_unit_price,2)); ?></td>
                                <td><?php echo e(number_format($total_amount,2)); ?></td>
                                <td>
                                    <?php if($values->relSelfQuotationSupplierByProposalId): ?>
                                    <?php $__currentLoopData = $values->relSelfQuotationSupplierByProposalId()->where('is_approved','approved')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button class="btn btn-sm btn-primary"><?php echo e($supplier->relSuppliers->name); ?></button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center action">
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName<?php echo e($values->id); ?>">
                                                <?php echo e(ucfirst($values->is_approved)); ?>

                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)">View</a>

                                            </li>

                                            <?php if($values->is_approved === 'approved'): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quotation-halt')): ?>
                                            <li>
                                                <a class="requisitionApprovedBtn" data-id="<?php echo e($values->id); ?>" data-status="halt"><?php echo e(__('Halt')); ?>

                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if($values->is_approved === 'approved'): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('generate-po')): ?> 
                                            <li>
                                                <a href="<?php echo e(route('pms.quotation.generate.po.process',$values->id)); ?>"><?php echo e(__('Generate PO')); ?></a>

                                                <a class="completeQuotation" data-id="<?php echo e($values->id); ?>" data-status="completeQuotation"><?php echo e(__('Complete')); ?></a>
                                            </li>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quotation-halt')): ?>
                                    <a href="<?php echo e(route('pms.quotation.quotations.cs.compare.view',['id'=>$values->relRequestProposal->id,'slug'=>'grid'])); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-border-all"></i></a>

                                    <a href="<?php echo e(route('pms.quotation.quotations.cs.compare.view',['id'=>$values->relRequestProposal->id,'slug'=>'list'])); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-list"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="p-3"> 
                       <?php if(count($quotationList)>0): ?>
                       <ul>
                        <?php echo e($quotationList->links()); ?>

                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>
</div>
</div>


<div class="modal" id="requisitionDetailModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quotations Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="tableData">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal" id="quotationHaldModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Hold the Quotations</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form action="<?php echo e(route('pms.quotation.halt.status')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="modal-body">

                    <div class="form-group">
                        <label for="remarks">Remarks :</label>

                        <input type="hidden" readonly required name="id" id="quotationId">

                        <textarea class="form-control" name="remarks" rows="3" id="remarks" placeholder="Write down here reason for hold"></textarea>

                    </div>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
    function openModal(quotation_id) {
        $('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/quotation-items")); ?>/'+quotation_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Quotation Details`);
        $('#requisitionDetailModal').modal('show');
    }

    function requestProposalDetails(request_proposal_id) {
        $('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/request-proposal-details")); ?>/'+request_proposal_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Proposal Details`);
        $('#requisitionDetailModal').modal('show');
    }

    (function ($) {
        "use script";

        $('#searchQuotationBtn').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let is_approved=$('#is_approved').val();
            let is_po_generate='yes';

            const searchPagination = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,is_approved:is_approved,is_po_generate:is_po_generate},
                            success:function (data) {
                                if(data.result == 'success'){
                                    $('#dataTableView').html(data.body);
                                    searchPagination();

                                }else{
                                    $('#dataTableView').html('<div><center>No Data Found !!</center></div>');
                                }
                            }
                        });
                    })

                });
                approveQa();
                generatePo();
            };

            if (from_date !='' || to_date !='' || is_approved) {
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,is_approved:is_approved,is_po_generate:is_po_generate},
                    success:function (data) {
                        if(data.result == 'success'){
                            $('#dataTableView').html(data.body);
                            searchPagination();
                        }else{
                            $('#dataTableView').html('<div><center>No Data Found !!</center></div>');
                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');

            }
        });

    //Approved Reject 
    const approveQa = () => {
        $('.requisitionApprovedBtn').on('click', function () {

            let id = $(this).attr('data-id');
            let status = $(this).attr('data-status');


            if (status==='halt'){

                $('#quotationId').val(id);
                return $('#quotationHaldModal').modal('show').on('hidden.bs.modal', function (e) {
                    let form = document.querySelector('#quotationHaldModal').querySelector('form').reset();

                })
            }

            $.ajax({
                url: "<?php echo e(url('pms/quotation/approved-status')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {_token: "<?php echo e(csrf_token()); ?>", id:id, status:status},
            })
            .done(function(response) {
                if(response.success){

                    $('#statusName'+id).html(response.new_text);
                    $('#removeRow'+id).hide();

                    notify(response.message,'success');
                }else{
                    notify(response.message,'error');
                }
            })
            .fail(function(response){
                notify('Something went wrong!','error');
            });
        });

    }
    approveQa();

    //generate po
   /* const generatePo = () => {
        $('.generatePoBtn').on('click', function () {

        let id = $(this).attr('data-id');
        $.ajax({
            url: "<?php echo e(url('pms/quotation/generate-po-store')); ?>",
            type: 'POST',
            dataType: 'json',
            data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
        })
        .done(function(response) {
             $('#removeRow'+id).hide();
            notify(response.message,response.result);
           
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
    });
    }
    generatePo();*/
    
      $('.completeQuotation').on('click', function () {
            let quotation_id = $(this).attr('data-id');
            swal({
                title: "<?php echo e(__('Are you sure?')); ?>",
                text: "<?php echo e(__('Once you Complete, You can not generate po from this quotation.')); ?>",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Complete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                $.ajax({
                    type: 'post',
                    url: '<?php echo e(url('pms/quotation/complete-quotation')); ?>',
                    dataType: "json",
                    data:{_token: "<?php echo e(csrf_token()); ?>", quotation_id:quotation_id},
                    success:function (data) {
                        if(data.result == 'success'){
                            $('#removeRow'+quotation_id).hide();
                             notify(data.message,data.result);
                        }else{
                            notify(data.message,data.result);
                        }
                    }
                });
                return false;
            }
        });
    });


})(jQuery)
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quotation/generate-po-list.blade.php ENDPATH**/ ?>