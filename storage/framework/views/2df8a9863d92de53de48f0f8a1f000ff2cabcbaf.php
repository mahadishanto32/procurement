
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
                                    <input type="text" name="from_date" id="from_date" class="form-control rounded  search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="to_date" id="to_date" class="form-control  search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="is_approved"><?php echo e(__('Status')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="is_approved" id="is_approved" class="form-control rounded">

                                        <option value="">Select Option</option>
                                        <option value="processing">Processing</option>
                                        <option value="approved">Approved</option>
                                        <option value="halt">Halt</option>
                                        
                                    </select>
                                </div>
                            </div>
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
                                    <th><?php echo e(__('Request Proposal')); ?></th>
                                    <th><?php echo e(__('Qty')); ?></th>
                                    <th><?php echo e(__('Unit Price')); ?></th>
                                    <th><?php echo e(__('Total Amount')); ?></th>
                                    <th><?php echo e(__('Supplier')); ?></th>
                                    <th class="text-center"><?php echo e(__('Status')); ?></th>
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

                                    foreach($values->relSelfQuotationSupplierByProposalId()->whereNotIn('is_approved',['pending','approved'])->get() as $supplier){

                                        $total_qty +=$supplier->relQuotationItems->sum('qty');
                                        $total_unit_price +=$supplier->relQuotationItems->sum('unit_price');
                                        $total_amount +=$supplier->relQuotationItems->sum('total_price');
                                    }
                                ?>
                                <tr>
                                    <td><?php echo e(($quotationList->currentpage()-1) * $quotationList->perpage() + $key + 1); ?></td>
                                    
                                    <td><a href="javascript:void(0)" class="btn btn-link" onclick="requestProposalDetails(<?php echo e($values->relRequestProposal->id); ?>)"><?php echo e($values->relRequestProposal->reference_no); ?></a></td>
                                    <td><?php echo e(number_format($total_qty,2)); ?></td>
                                    <td><?php echo e(number_format($total_unit_price,2)); ?></td>
                                    <td><?php echo e(number_format($total_amount,2)); ?></td>
                                    <td>
                                        <?php if($values->relSelfQuotationSupplierByProposalId): ?>
                                        <?php $__currentLoopData = $values->relSelfQuotationSupplierByProposalId()->whereNotIn('is_approved',['pending','approved'])->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <button class="btn btn-sm btn <?php echo e($supplier->is_approved=='halt'?' btn-warning':'btn-info'); ?>"><?php echo e($supplier->relSuppliers->name); ?></button>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>


                                    <td class="text-center action">
                                        <?php
                                            $approvedCount = \App\Models\PmsModels\Quotations::where('request_proposal_id',$values->request_proposal_id)->where('is_approved','approved')->count();
                                        ?>
                                        <?php if($approvedCount > 0): ?>
                                        <a class="btn btn-xs btn-success">Approved Once</a>
                                        <?php else: ?>
                                        <a class="btn btn-xs btn-warning">Waiting for Approval</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center action">
                                    
                                     <a href="<?php echo e(route('pms.quotation.quotations.cs.compare.view',['id'=>$values->request_proposal_id,'slug'=>'grid'])); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-border-all"></i></a>

                                        <a href="<?php echo e(route('pms.quotation.quotations.cs.compare.view',['id'=>$values->request_proposal_id,'slug'=>'list'])); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-list"></i></a>

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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quotation Comparison</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
            
        
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" id="qty-submit-btn" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
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
        $('.modal-body').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/quotation-items")); ?>/'+quotation_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Quotation Details`);
        $('#requisitionDetailModal').modal('show');
    }

    function requestProposalDetails(request_proposal_id) {
        $('.modal-body').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/request-proposal-details")); ?>/'+request_proposal_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Request Proposal Details`);
        $('#requisitionDetailModal').find('.modal-body #qty-submit-btn').hide();
        $('#requisitionDetailModal').modal('show');
    }


    (function ($) {
        "use script";

        $('#searchQuotationBtn').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let is_approved=$('#is_approved').val();
            let is_po_generate='no';


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

                    $('#quotationId').val(id)
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

})(jQuery)
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quotation/approval-index.blade.php ENDPATH**/ ?>