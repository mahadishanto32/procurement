

<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>

<?php $__env->startSection('main-content'); ?>
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
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
              <li class="active"><?php echo e(__($title)); ?> List</li>
        </ul>
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

                        <div class="col-md-2 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="status"><?php echo e(__('Status')); ?>:</label></p>
                            <div class="input-group input-group-md mb-3 d-">
                                <select name="status" id="status" class="form-control rounded">
                                    <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                    <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($values); ?>"><?php echo e(ucfirst($values)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="searchDeliveredRequisitonBtn"></label></p>
                            <div class="input-group input-group-md">
                                <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.store-manage.store.delivered.requistion.search')); ?>" id="searchDeliveredRequisitonBtn"> <i class="las la-search"></i>Search</a>
                            </div>
                        </div>

                    </div>                    
                </div>
                <div class="panel-body" id="viewResult">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="2%">SL No</th>
                                <th>Unit</th>
                                <th>Department</th>
                                <th>Requisition Date</th>
                                <th>Delivered Date</th>
                                <th>Requisition RefNo</th>
                                <th>Delivered RefNo</th>
                                <th>Category</th>
                                <th>SubCategory</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th class="text-center">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($deliveredRequisition)): ?>
                            <?php $__currentLoopData = $deliveredRequisition; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td width="5%"><?php echo e(($deliveredRequisition->currentpage()-1) * $deliveredRequisition->perpage() + $key + 1); ?></td>
                                <td>
                                    <?php echo e($values->relRequisitionDelivery->relRequisition->relUsersList->employee->unit->hr_unit_short_name?$values->relRequisitionDelivery->relRequisition->relUsersList->employee->unit->hr_unit_short_name:''); ?>

                                </td>
                                <td>
                                    <?php echo e($values->relRequisitionDelivery->relRequisition->relUsersList->employee->department->hr_department_name?$values->relRequisitionDelivery->relRequisition->relUsersList->employee->department->hr_department_name:''); ?>

                                </td>

                                <td><?php echo e(date('d-m-Y',strtotime($values->relRequisitionDelivery->relRequisition->requisition_date))); ?></td>

                                <td><?php echo e(date('d-m-Y',strtotime($values->relRequisitionDelivery->delivery_date))); ?></td>

                                <td><a href="javascript:void(0)" data-id="<?php echo e($values->relRequisitionDelivery->relRequisition->id); ?>"  class="btn btn-link requisitionview m-1 rounded"><?php echo e($values->relRequisitionDelivery->relRequisition->reference_no); ?></a></td>
                                <td>
                                    <?php echo e($values->relRequisitionDelivery->reference_no); ?>

                                </td>

                                <td>
                                    <?php echo e($values->product->category->category->name); ?>

                                </td>

                                <td>
                                    <?php echo e($values->product->category->name); ?>

                                </td>
                                <td><?php echo e($values->product->name); ?></td>
                                <td><?php echo e(number_format($values->delivery_qty,0)); ?></td>

                                <td class="text-center" id="action<?php echo e($values->id); ?>">
                                    <?php if($values->status=='pending' || $values->status=='acknowledge'): ?>
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName<?php echo e($values->id); ?>">
                                                <?php echo e(ucfirst($values->status)); ?>

                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                           <li id="hideBtn<?php echo e($values->id); ?>"><a href="javascript:void(0)" class="StoredeliveredAcknowledge" data-id="<?php echo e($values->id); ?>" title="Delivered"><i class="la la-check"></i> <?php echo e(__('Delivered')); ?></a>
                                           </li>
                                       </ul>
                                   </div>
                                   <?php else: ?>
                                   Delivered
                                   <?php endif; ?>
                               </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>

                    </table>
                    <div class="la-1x text-center">
                        <?php if(count($deliveredRequisition)>0): ?>
                            <ul>
                                <?php echo e($deliveredRequisition->links()); ?>

                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<!-- Requisition Details Modal Start -->
<div class="modal" id="requisitionDetailModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Requisition Details</h4>
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
<!-- Requisition Details Modal End -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>

    
    (function ($) {
        "use script";
        
         $('#searchDeliveredRequisitonBtn').on('click', function () {
            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let status=$('#status').val();

            const searchDeliveredRequisitonBtn = () => {
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
                            data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,status:status},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchDeliveredRequisitonBtn();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
                StoredeliveredAcknowledge();
                requisitionview();
            };

            if (from_date !='' || to_date !='' || status) {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,status:status},
                    success:function (data) {
                        if(data.result == 'success'){

                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchDeliveredRequisitonBtn();
                            requisitionview();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');
                        }   
                    }
                });
                return false;
            }else{
                notify('Please enter data & status first !!','error');

            }
        });

        const StoredeliveredAcknowledge = () => {
            $('.StoredeliveredAcknowledge').on('click', function () {

                let id = $(this).attr('data-id');
                $.ajax({
                    url: "<?php echo e(url('pms/store-manage/store-delivered-requistion-ack')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
                })
                .done(function(response) {
                    if(response.result=='success'){
                        $('#statusName'+id).html('Delivered');
                        $('#hideBtn'+id).hide();
                        notify(response.message,response.result);
                    }
                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
                return false;
            });
        };
        StoredeliveredAcknowledge();

        const requisitionview = ()=>{
            $('.requisitionview').on('click', function () {
                let id = $(this).attr('data-id');
                $.ajax({
                    type: 'post',
                    url: "<?php echo e(url("pms/store-manage/store-delivered-requistion-view")); ?>",
                    data:{_token:'<?php echo csrf_token(); ?>',id:id},
                    success:function (data) {
                        if(data.result == 'success'){
                            showPreloader('none');
                            $('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
                            $('#tableData').html(data.body);
                            $('#requisitionDetailModal').modal('show');

                        }else{
                            showPreloader('none');
                        }   
                    }
                   
                });
                
                return false;
            });
        };

        requisitionview();

    })(jQuery);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/store/store-delivered-requisition-list.blade.php ENDPATH**/ ?>