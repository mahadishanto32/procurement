<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/all.js')); ?>"></script>
<!-- jQuery Confirm alert -->
<script src="<?php echo e(asset('assets/js/jquery-confirm/jquery-confirm.min.js')); ?>"></script>

<!-- toastr alert -->
<script src="<?php echo e(asset('notification_assets/js/toastr.min.js')); ?>"></script>
<!-- sweet alert -->
<script src="<?php echo e(asset('notification_assets/js/sweetalert.min.js')); ?>"></script>
<script src="<?php echo e(asset('plugins/summernote/summernote.min.js')); ?>"></script>
<script src="https://cdn.ckeditor.com/4.18.0/standard/ckeditor.js"></script>
<!-- BOOTSTRAP select -->



<!-- datatable-exportable CDN -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/fc-3.3.1/fh-3.1.7/r-2.2.5/sc-2.0.2/datatables.min.js"></script>
<!-- datatable-exportable CDN -->

<!-- datatable-exportable innitialization -->
<script type="text/javascript">
    $(document).ready(function() {
        var datatable_file_name = $('.datatable-exportable').attr('data-table-name');
        var table = $('.datatable-exportable').DataTable({
            lengthMenu: [
                [ 5,10, 25, 50, 100, -1 ],
                [ '5 rows', '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
            ],

            language: {
              emptyTable: "No data available right now"
            },
            
            iDisplayLength: -1,

            // sScrollX: "100%",
            
            sScrollXInner: "100%",
            scrollCollapse: true,

            paging: false,
            //ordering: false,
            info: false,

            dom: 'Bfrtip',
            buttons: [
                // 'pageLength',
                {
                    extend: 'copy',
                    title: datatable_file_name
                },
                {
                    extend: 'print',
                    title: datatable_file_name
                },
                {
                    extend: 'csv',
                    filename: datatable_file_name
                },
                {
                    extend: 'excel',
                    filename: datatable_file_name
                },
                {
                    extend: 'pdf',
                    filename: datatable_file_name
                },
            ],


        });
        
        $('.buttons-collection').addClass('btn-sm');
        $('.buttons-copy').removeClass('btn-secondary').addClass('btn-sm btn-warning').html('<i class="las la-copy"></i>').attr('title', "Copy");
        $('.buttons-csv').removeClass('btn-secondary').addClass('btn-sm btn-success').html('<i class="las la-file-csv"></i>').attr('title', "CSV");
        $('.buttons-excel').removeClass('btn-secondary').addClass('btn-sm btn-primary').html('<i class="lar la-file-excel"></i>').attr('title', "Excel");
        $('.buttons-pdf').removeClass('btn-secondary').addClass('btn-sm btn-dark').html('<i class="las la-file-pdf"></i>').attr('title', "PDF");
        $('.buttons-print').removeClass('btn-secondary').addClass('btn-sm btn-dark').html('<i class="las la-print"></i>').attr('title', "Print");

        $('.datatable-exportable').parent().addClass('table-responsive')
    });
</script>
<!-- datatable-exportable innitialization -->

<script>
    var count = 0;
    var refreshIntervalId =setInterval(function(){
        count++;
        jQuery(document).ready(function() {
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");

        });
        if( count == 5){
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");
        }
    }, 300);
</script>
<script>
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    let afterLoader = '<div class="loading-select left"><img src="<?php echo e(asset('images/loader.gif')); ?>" /></div>';
</script>
<!-- Custom JavaScript -->
<?php echo $__env->yieldPushContent('js'); ?>
<?php echo app('toastr')->render(); ?>
<script>
    $( document ).ajaxComplete(function() {
        // Required for Bootstrap tooltips in DataTables
        $('[data-toggle="tooltip"]').tooltip({
            "html": true,
            //"delay": {"show": 1000, "hide": 0},
        });
        $('[data-toggle="popover"]').popover({
            html: true,
        });
    });

    $('[data-toggle="popover"]').click(function () {
        $(this).popover('show');
    });


    $(document).ajaxError(function(event, jqxhr, settings, exception) {
        if (exception == 'Unauthorized') {
            $.notify("Your session has expired!", 'error');
            setTimeout(function(){
                window.location = '<?php echo e(url()->full()); ?>';
            }, 1000)
        }
    });
    let panelOptions = [];
    let Scrollbar = window.Scrollbar;
    if (jQuery('.col-panel-scroll').length) {
        Scrollbar.init(document.querySelector('.col-panel-scroll'), panelOptions);
    }
    let Scrollbar1 = window.Scrollbar;
    if (jQuery('.col-panel-scroll1').length) {
        Scrollbar1.init(document.querySelector('.col-panel-scroll1'), panelOptions);
    }
    let Scrollbar2 = window.Scrollbar;
    if (jQuery('.col-panel-scroll2').length) {
        Scrollbar2.init(document.querySelector('.col-panel-scroll2'), panelOptions);
    }
    // on first focus (bubbles up to document), open the menu
    $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).closest(".select2-container").siblings('select:enabled').select2('open');
    });
    // steal focus during close - only capture once and stop propogation
    $('select.select2').on('select2:closing', function (e) {
        $(e.target).data("select2").$selection.one('focus focusin', function (e) {
            e.stopPropagation();
        });
    });

    //Notify using swal
    function notify(message,type) {
        swal({
            icon: type,
            text: message,
            button: false
        });
        setTimeout(()=>{
            swal.close();
    }, 1000);
    }
    //select 2
    $(document).ready(function() {
        var this_link = "<?php echo e(str_replace(url('/'), '', request()->url())); ?>";
        if(this_link == "" || this_link == "/pms"){
            jQuery.noConflict()
        }
        
        $('.select2').select2();

        $(".select2-tags").select2({
          tags: true
        });

        $.each($('.notification-links'), function(index, val) {
            var link = $(this);
            link.css('cursor', 'pointer !important');
            link.click(function(event) {
                var modal = $('#notificationModal');
                modal.find('.modal-title').html(link.attr('data-title'));
                modal.modal('show');
                modal.find('.modal-body').html('<h3 class="text-center">Please wait...</h3>');
                $.ajax({
                    url: link.attr('data-src'),
                    type: 'GET',
                    data: {},
                })
                .done(function(response) {
                    modal.find('.modal-body').html(response);
                    markAsRead(link.parent().attr('data-notification-id'));
                });
            });
        });
    });

    $(document).ready(function() {
      //$('.summernote').summernote();
      CKEDITOR.replaceAll( 'summernote' );
    });
</script>


<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
<script src="<?php echo e(asset('js/custom.js')); ?>"></script>

<script>
    (function ($) {
        "use script";
        let ulIqMenu = document.querySelector('.iq-menu');
        let liIqMenu = ulIqMenu.querySelectorAll('li.iq-menu-main');
        $.map(liIqMenu, (item, key) => {
            if(!item.querySelector("ul")){
                let elementUrl = item.querySelector('a').getAttribute('href').replace(`${window.location.protocol}//${window.location.host}`,'')
                elementUrl = elementUrl.split("/").filter(function (el) {
                    return el != "";
                });
                let activeUrl = window.location.pathname;
                activeUrl = activeUrl.split("/").filter(function (el) {
                    return el != "";
                });
                if(elementUrl.length <= 1 && activeUrl.length <= 1){
                    if(elementUrl[0] === activeUrl[0]) item.classList.add("active");
                }else if(elementUrl.length > 1 && activeUrl.length > 1) {
                    if(elementUrl[1] === activeUrl[1]) item.classList.add("active");
                }
            }
        });
        let subIqMenu = ulIqMenu.querySelectorAll('ul');
        $.map(subIqMenu, function (item, key) {
            let subMenu = item.querySelectorAll('li');
            $.map(subMenu, function (element, index) {
                let browseUrl = window.location.href;
                let activeUrl = window.location.pathname;
                activeUrl = activeUrl.split("/")[2];
                let elementUrl = element.childNodes[0].getAttribute('href').replace(`${window.location.protocol}//${window.location.host}`,'').split("/")[2]
                if(activeUrl === elementUrl){
                    element.class = "active main-active"
                    element.parentElement.classList.add('show')
                    element.parentElement.parentElement.classList.add('active')
                }
            })
        });

    })(jQuery);
</script>

<!-- Datetime picker -->
<script src="<?php echo e(asset('plugins/air-datepicker/js/datepicker.min.js')); ?>"></script>

<script type="text/javascript">
    $('.air-datepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        timepicker: true,
    });

     $('.search-datepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        timepicker: false,
    });



    const showPreloader = (value) => {
            if (value==='none') {
                document.getElementById("loading").setAttribute("style", "display:none");
            }else{
                document.getElementById("loading").setAttribute("style", "display:block");
            }
        };


    function markAsRead(id) {
      $.ajax({
            url: "<?php echo e(url('pms/requisition/mark-as-read')); ?>",
            type: 'POST',
            dataType: 'json',
            data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#read'+id).hide();
                $('.notification-count').html(response.total_notification);
                //notify(response.message,response.result);
            }
        })
        .fail(function(response){
            //notify('Something went wrong!','error');
        });
        //return false;
    }
</script><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/layouts/script.blade.php ENDPATH**/ ?>