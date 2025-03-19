<?php $__env->startPush('css'); ?>
	<style>	
	.nav-search .nav-search-input { width: 100%;}
	</style>
<?php $__env->stopPush(); ?>

<?php if(isset($_REQUEST['search'])): ?>
	<?php $value = $_REQUEST['search']; ?>
<?php else: ?>
	<?php $value = ''; ?>
<?php endif; ?>

<div class="iq-search-bar">
    <form action="<?php echo e(url('/search')); ?>" method="get" class="searchbox" id="form-seach">
       <input type="text" name="search" placeholder="Search Employee..." class="text search-input typeahead seach-employee" placeholder="Type here to search..." value="<?php echo e($value); ?>" id="nav-search-input1" autocomplete="off" required data-type="employee">
       <a class="search-link" href="#"><i class="las la-user-circle"></i></a>
    </form>
    <div id="search-suggestion" style="position: relative">
    	
    </div>
 </div>
<?php $__env->startPush('js'); ?>
	
	<script>

		$(document).on('keyup', '.seach-employee', function (e) {
			var keyword = $('.seach-employee').val();
			if(keyword !== '' && keyword !== null){
				if (e.which == 13) {
						$('form#form-seach').submit();
				    	return false; 
				}else{
					$.ajax({
			            url: '<?php echo e(url("search/suggestion")); ?>',
			            data: {
			                keyword: keyword,
			                _token : "<?php echo e(csrf_token()); ?>",
			            },
			            type: 'post',
			            success: function(result)
			            {  
			                $('#search-suggestion').html(result);
			            },
			            error:function(xhr)
			            {
			            	$('#search-suggestion').html('');
			            }
			        });
				}
			}else{
				$('#search-suggestion').html('');
			}
			
		  
		});
	</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/common/top_search.blade.php ENDPATH**/ ?>