
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
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form  method="post" action="<?php echo e(route('pms.quotation.quotations.generate.po.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="po_date"><?php echo e(__(' Date')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="po_date" id="po_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm"  required readonly value="<?php echo e(old('po_date')?old('po_date'):date('d-m-Y h:i:s')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="rfp_reference_no"><?php echo e(__('Quotation Ref No')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="rfp_reference_no" id="rfp_reference_no" class="form-control rounded" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(($quotation->relRequestProposal->reference_no)?($quotation->relRequestProposal->reference_no):0); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="quotation_ref_no"><?php echo e(__('Quotation Ref No')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="quotation_ref_no" id="quotation_ref_no" class="form-control rounded" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(($quotation->reference_no)?($quotation->reference_no):0); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="hr_unit_id"><?php echo e(__('Unit')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="hr_unit_id" id="hr_unit_id" class="form-control rounded" onchange="getRequisitions()">
                                            <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                            <?php if($units): ?>
                                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($unit); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="hr_department_id"><?php echo e(__('Department')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="hr_department_id" id="hr_department_id" class="form-control rounded" onchange="getRequisitions()">
                                            <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                            <?php if($departments): ?>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($department); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="requisition_id"><?php echo e(__('Requisitions')); ?> *:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="requisition_id[]" id="requisition_id" class="form-control rounded select2 select2-tags requisition-wise-items" multiple>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-10">
                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Quotation Qty</th>
                                            <th>Unit Total</th>
                                            <th>Requisition Qty</th>
                                            <th width="10%">PO Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fetchQuotation">
                                        <?php $__currentLoopData = $quotation->relQuotationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($key+1); ?></td>
                                            <td><?php echo e($item->relProduct->category->name); ?></td>
                                            <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->relProduct->id)); ?>)</td>
                                            <td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                                            <td><?php echo e($item->unit_price); ?></td>
                                            <td><?php echo e($item->qty); ?></td>
                                            <td><?php echo e(number_format($item->total_price,2)); ?></td>
                                            <td>
                                                <input type="number" id="item-<?php echo e($item->id); ?>" name="po_qty[<?php echo e($item->product_id); ?>]"class="form-control bg-white po-qty" data-id="<?php echo e($item->product_id); ?>" readonly>
                                            </td>

                                            <td>
                                                <input type="number" id="po-<?php echo e($item->id); ?>" name="po_qty[<?php echo e($item->product_id); ?>]"class="form-control bg-white po-qty check-po-qty" data-id="<?php echo e($item->product_id); ?>" onkeypress="return isNumberKey(event);" onchange="checkPOQty()" onkeyup="checkPOQty()">
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td colspan="4" class="text-right">Total</td>
                                            <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('unit_price'),2)); ?></td>
                                            <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('qty'),2)); ?></td>
                                            <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('total_price'),2)); ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="6" class="text-right">(-) Discount</td>
                                            <td><?php echo e(number_format($quotation->discount,2)); ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-right">(+) Vat</td>
                                            <td><?php echo e(number_format($quotation->vat,2)); ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-right">Total Amount</td>
                                            <td><?php echo e(number_format($quotation->gross_price,2)); ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        
                                        <input type="hidden" class="quotation-id" name="quotation_id" value="<?php echo e($quotation->id); ?>">
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="form-row">
                                <div class="col-12">
                                    <p class="mb-1 font-weight-bold"><label for="remarks"><?php echo e(__('Notes')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <textarea name="remarks" id="remarks" class="form-control rounded" rows="3" cols="5" placeholder="Write here..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-success rounded"><?php echo e(__('Generate PO')); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
    (function ($) {
        "use strcit";

        const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };


        const requisitionItemSum = () => {
            $('.requisition-wise-items').on('change', function () {
                let requisitionId= $(this).val();
                let quotationId= $('.quotation-id').val();
            if (requisitionId !="") {
                $.ajax({
                    url: "<?php echo e(url('pms/quotation/requisition-wise-item-qty')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token:'<?php echo csrf_token(); ?>',requisitionId:requisitionId,quotationId:quotationId},
                    success:function (response) {

                        $.each(response, function(item_id, po_qty) {
                            $('#item-'+item_id).val(po_qty);
                        });

                        checkPOQty();
                    }
                });
            }else{
                $('.po-qty').val(0);
                checkPOQty();
            }
        })
        }
        requisitionItemSum();

    })(jQuery);

    getRequisitions();
    function getRequisitions() {
        let hrUnitId = $('#hr_unit_id').val() !="" ? $('#hr_unit_id').val() : 0;
        let hr_department_id = $('#hr_department_id').val() !="" ? $('#hr_department_id').val() : 0;
        let quotationId= $('.quotation-id').val();
        $.ajax({
            url: "<?php echo e(url('pms/quotation/unit-wise-requisition')); ?>/"+hrUnitId+'/'+quotationId+"?hr_department_id="+hr_department_id,
            type: 'GET',
            dataType: 'json',
            data: {},
        })
        .done(function(response) {
            var requisition='<option value="">Select Requisition</option>';
            $.each(response, function(index, val) {
                requisition+='<option value="'+val.id+'">'+val.reference_no+'</option>';
            });
            $('#requisition_id').html(requisition).change();
            $('.po-qty').val(0);
            checkPOQty();
        })
        .fail(function() {
            $('#requisition_id').html('').change();
            checkPOQty();
        });
    }

    function checkPOQty() {
        $.each($('.check-po-qty'), function(event){
            var po_qty = parseInt($(this).val());
            var requision_qty = parseInt($(this).parent().prev().find('input').val());
            var quotation_qty = parseInt($(this).parent().prev().prev().prev().text());

            if(requision_qty <= 0 || po_qty > quotation_qty){
                po_qty = (requision_qty > 0 ? po_qty  : 0);
                po_qty = (po_qty <= quotation_qty ? po_qty : quotation_qty);

                $(this).val(po_qty);
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quotation/generate-po-process.blade.php ENDPATH**/ ?>