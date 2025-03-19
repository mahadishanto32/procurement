

<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>

<?php $__env->startSection('page-css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<?php
$modifiedName=false;
?>

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
                <li class="top-nav-btn">
                   <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
               </li>
           </ul>
       </div>

       <div class="page-content">
        <div class="">
            <div class="panel panel-info">

                <form action="<?php echo e(route('pms.requisition.requisition.update',$requisition->id)); ?>" method="POST" id="editRequisitionForm">
                    <input type="hidden" name="_method" value="PUT">
                    <?php echo csrf_field(); ?>
                    <div class="panel-body">
                        <div class="row">


                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="reference"><?php echo e(__('Reference No.')); ?>:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="reference_no" id="reference" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(old('reference_no',$requisition->reference_no)); ?>">


                                    <?php if($errors->has('reference_no')): ?>
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo e($errors->first('reference_no')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="date"><?php echo e(__('Date')); ?>:</label> </p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="requisition_date" id="date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(date('Y-m-d',strtotime($requisition->requisition_date))); ?>" >
                                </div>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('project-action')): ?>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="project_id"><?php echo e(__('Select Project')); ?>:</label> </p>

                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="project_id" id="project_id" class="form-control" data-url="<?php echo e(route("pms.requisition.load-project-wise-deliverables")); ?>">
                                        <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($project->id); ?>" <?php echo e($requisition->project_id==$project->id?'selected':''); ?>><?php echo e($project->name.' ('.$project->indent_no.')'); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="project_id"><?php echo e(__('Select Deliverables')); ?>:</label> </p>

                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="deliverable_id" id="deliverable_id" class="form-control">
                                        <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                        <?php $__currentLoopData = $deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deliverable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($deliverable->id); ?>" <?php echo e($requisition->deliverable_id==$deliverable->id?'selected':''); ?>><?php echo e($deliverable->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-12  table-responsive style-scroll">

                                <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Category')); ?></th>
                                            <th><?php echo e(__('Sub Category')); ?></th>
                                            <th width="50%"><?php echo e(__('Product')); ?></th>
                                            <th width="10%"><?php echo e(__('Qty')); ?></th>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-requisition-edit')): ?>
                                            <th width="10%"><?php echo e(__('Approved Qty')); ?></th>
                                            <?php 
                                            $modifiedName=true;
                                            ?>
                                            <?php endif; ?>

                                            <th class="text-center"><?php echo e(__('Action')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="field_wrapper">

                                        <?php
                                        $oldProductIds=[];
                                        ?>
                                        <?php $__empty_1 = true; $__currentLoopData = $requisition->requisitionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$requisitionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="category_id" id="category_<?php echo e($key); ?>" class="form-control category">
                                                        <option value="<?php echo e(null); ?>"><?php echo e(__('Select Category')); ?></option>

                                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>" <?php echo e($requisitionItem->product->category->parent_id==$category->id?'selected':''); ?>>
                                                            <?php echo e($category->name.'('.$category->code.')'); ?>

                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="sub_category_id[]" id="subCategoryId_<?php echo e($key); ?>" class="form-control subcategory" onchange="getProduct($(this))">
                                                        <option value="<?php echo e(null); ?>"><?php echo e(__('Select SubCategory')); ?></option>

                                                        <?php if(isset($subCategories[0])): ?>
                                                        <?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($subCat->parent_id == $requisitionItem->product->category->parent_id): ?>
                                                        <option value="<?php echo e($subCat->id); ?>" data-selected-product="<?php echo e($requisitionItem->product_id); ?>" <?php echo e($requisitionItem->product->category_id == $subCat->id ? 'selected' : ''); ?>><?php echo e($subCat->name); ?> (<?php echo e($subCat->code); ?>)</option>
                                                        <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>

                                            </td>

                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <?php
                                                    array_push($oldProductIds,$requisitionItem->product->id);
                                                    ?>
                                                    <select name="product_id[]" id="product_<?php echo e($key); ?>" class="form-control product" required>
                                                        <option value="<?php echo e($requisitionItem->product->id); ?>"><?php echo e(__($requisitionItem->product->name)); ?> (<?php echo e(getProductAttributes($requisitionItem->product_id)); ?>)</option>
                                                    </select>
                                                </div>

                                            </td>
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" <?php if($modifiedName): ?> readonly name="old_qty[]" value="<?php echo e(old('qty',$requisitionItem->requisition_qty)); ?>" <?php else: ?> name="qty[]" value="<?php echo e(old('qty',$requisitionItem->qty)); ?>" <?php endif; ?>  min="1" max="99999999" id="qty_<?php echo e($key); ?>" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required>
                                                </div>
                                            </td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-requisition-edit')): ?>
                                            <td>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" name="qty[]" min="1" max="99999999" id="qty_<?php echo e($key); ?>" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('qty',$requisitionItem->qty)); ?>">
                                                </div>
                                            </td>
                                            <?php endif; ?>

                                            <td>
                                                <a href="javascript:void(0);" id="remove_<?php echo e($key); ?>" class="remove_button btn btn-danger btn-sm" style="margin-right:17px;" title="Remove" >
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                            
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>
                                <?php if(auth::user()->id ==$requisition->author_id): ?>
                                <a href="javascript:void(0);" style="margin-right:27px;" class="add_button btn btn-sm btn-primary pull-right" title="Add More Product">
                                    <i class="las la-plus"></i>
                                </a>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-12">
                                <p class="mb-1 font-weight-bold"><label for="remarks"><?php echo e(__('Notes')); ?>:</label> <?php echo $errors->has('remarks')? '<span class="text-danger text-capitalize">'. $errors->first('remarks').'</span>':''; ?></p>
                                <div class="form-group form-group-lg mb-3 d-">
                                    <textarea rows="3" name="remarks" id="remarks" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm"><?php echo old('remarks',$requisition->remarks); ?></textarea>
                                </div>

                                <input type="hidden" name="status" value="<?php echo e($requisition->status); ?>">

                                <?php if($modifiedName): ?>
                                <input type="hidden" name="approval_qty" value="true">
                                <input type="hidden" name="project_id" value="<?php echo e($requisition->project_id); ?>">
                                <input type="hidden" name="deliverable_id" value="<?php echo e($requisition->deliverable_id); ?>">
                                <?php else: ?>
                                <input type="hidden" name="approval_qty" value="false">
                                <?php endif; ?>
                                <input type="hidden" name="hr_unit_id" value="<?php echo e($requisition->hr_unit_id); ?>">
                                <input type="hidden" name="author_id" value="<?php echo e($requisition->author_id); ?>">
                                <input type="hidden" name="created_by" value="<?php echo e($requisition->created_by); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Notes History
                                </button>
                                
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-danger rounded pull-right"><?php echo e(__('Update Requisition')); ?></button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>

            <div class="panel-body">
                <div class="collapse" id="collapseExample">
                  <div class="row">
                    <?php $__currentLoopData = $requisition->requisitionNoteLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 <?php echo e(in_array($log->type, ['department-head']) ? 'offset-md-6' : ''); ?>">
                        <div class="panel">
                            <div class="panel-body">
                                <p><?php echo e($log->notes); ?></p>
                                <br>
                                <small><?php echo e($log->createdBy->name); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo e(ucwords(implode(' ', explode('-', $log->type)))); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo e(date('Y-m-d g:i a', strtotime($log->created_at))); ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script type="text/javascript">
    var selectedProductIds=["<?php echo e(implode(",",$oldProductIds)); ?>"];

    function changeSelectedProductIds() {
        selectedProductIds=[];
        $('.product').each(function () {
            selectedProductIds.push($(this).val());
        })
    }

    $(document).ready(function(){
        var maxField = 500;
        var addButton = $('.add_button');
        var x = 1; 
        var wrapper = $('.field_wrapper');
        $(addButton).click(function(){
            x++;

            var fieldHTML = '<tr>\n' +
            '                                            <td>\n' +
            '                                              <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                <select name="category_id" id="category_'+x+'" class="form-control category select2">\n' +
            '                                                    <option value="<?php echo e(null); ?>"><?php echo e(__("Select Category")); ?></option>\n' +
            '                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>\n' +
            '                                                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name."(".$category->code.")"); ?></option>\n' +
            '                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>\n' +
            '                                                </select>\n' +
            '                                              </div>\n' +
            '                                            </td>\n' +
            '<td>\n' +
            '                                                    <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                        <select name="sub_category_id[]" id="subCategoryId_'+x+'" class="form-control subcategory" placeholder="Select Sub Category" onchange="getProduct($(this))">\n' +
            '                                                    <option value="<?php echo e(null); ?>"><?php echo e(__("Select Subcategory")); ?></option>\n' +
            '                                                    <?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>\n' +
            '                                                        <option value="<?php echo e($subCategory->id); ?>"><?php echo e($subCategory->name."(".$subCategory->code.")"); ?></option>\n' +
            '                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>\n' +
            '                                                </select>\n' +
            '                                                    </div>\n' +
            '\n' +
            '                                                </td>'+

            '                                            <td>\n' +
            '\n' +
            '                                                <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                    <select name="product_id[]" id="product_'+x+'" class="form-control select2 product" required>\n' +
            '                                                        <option value="<?php echo e(null); ?>"><?php echo e(__("--Select Product--")); ?></option>\n' +
            '                                                    </select>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                            </td>\n' +
            '                                            <td>\n' +
            '                                                <div class="input-group input-group-md mb-3 d-">\n' +
            '                                                    <input type="number" name="qty[]" min="1" max="99999999" id="qty_'+x+'" onKeyPress="if(this.value.length==6) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old("qty")); ?>">\n' +
            '                                                </div>\n' +
            '                                            </td>\n'+'<?php if($modifiedName): ?>\n' +
            '<td></td>\n'+'<?php endif; ?>\n' +
            '                                            <td>\n' +
            '                                                <a href="javascript:void(0);" id="remove_'+x+'" class="remove_button btn btn-sm btn-danger" title="Remove" >\n' +
            '                                                    <i class="las la-trash"></i>\n' +
            '                                                </a>\n' +
            '                                            </td>\n' +
            '\n' +
            '                                        </tr>';

            $(wrapper).append(fieldHTML);
            $('#category_'+x, wrapper).select2();
            $('#subCategoryId_'+x, wrapper).select2();
            $('#product_'+x, wrapper).select2();

            getProduct($('#subCategoryId_'+x, wrapper));
        });


            //Once remove button is clicked
            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();
                x--;

                var incrementNumber = $(this).attr('id').split("_")[1];
                var productVal=$('#product_'+incrementNumber).val()

                const index = selectedProductIds.indexOf(productVal);
                if (index > -1) {
                    selectedProductIds.splice(index, 1);
                }
                $(this).parent('td').parent('tr').remove();
                
            });


        });
    </script>

    <script>
        $(document).ready(function() {
            $.each($('.subcategory'), function(index, val) {
                getProduct($(this));
            });

            var wrapper = $('.field_wrapper');

            $(wrapper).on('change', '.category', function (e) {
                changeSelectedProductIds();
                var incrementNumber = $(this).attr('id').split("_")[1];
                //$('#qty_'+incrementNumber).val('');
                $('#product_'+incrementNumber).val('').select2();

                var categoryId = $('#category_' + incrementNumber).val();
                if (categoryId.length === 0) {
                    categoryId = 0;
                }
                $('#subCategoryId_' + incrementNumber).html('<center><img src=" <?php echo e('<i class="fa fa-spinner"></i>'); ?>"/></center>').load('<?php echo e(URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-subcategory")); ?>/' + categoryId);

                $('#product_' + incrementNumber).html('<center><img src=" <?php echo e('<i class="fa fa-spinner"></i>'); ?>"/></center>').load('<?php echo e(URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")); ?>/' + categoryId+'?products_id='+selectedProductIds);
            });

            $(wrapper).on('change','.product', function (e) {
                changeSelectedProductIds();
                var incrementNumber = $(this).attr('id').split("_")[1];
                //$('#qty_'+incrementNumber).val('');

                $(this).parent().parent().parent().find('.category').val(parseInt($(this).find(':selected').attr('data-category-id'))).select2();
                $(this).parent().parent().parent().find('.subcategory').val(parseInt($(this).find(':selected').attr('data-sub-category-id'))).select2();
            });

        });

        function getProduct(element){
            var incrementNumber = element.attr('id').split("_")[1];

            changeSelectedProductIds();

            var subcategory_id = $('#subCategoryId_' + incrementNumber).val();
            var selected_product = $('#subCategoryId_' + incrementNumber).find(':selected').attr('data-selected-product');

            if (subcategory_id.length === 0) {
                subcategory_id = 0;
            }
            //$('#qty_'+incrementNumber).val('')
            $('#product_' + incrementNumber).html('<center><img src=" <?php echo e('<i class="fa fa-spinner"></i>'); ?>"/></center>').load('<?php echo e(URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")); ?>/' + subcategory_id+'?products_id='+selectedProductIds+"&selected="+selected_product);
        }


        (function ($){
            "use script";
            $("#project_id").on('change', (e)=> {
                let project = e.target.value;
                $("#deliverable_id").empty();
                if(project) {
                    $.ajax({
                        type: 'get',
                        url: `${e.target.getAttribute("data-url")}/${project}`,
                        success: (data) => {
                            $("#deliverable_id").empty().append(data)
                        },
                    })
                }
            });
        })(jQuery);
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/requisitions/edit.blade.php ENDPATH**/ ?>