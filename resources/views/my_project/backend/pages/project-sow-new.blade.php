@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
    <style type="text/css">
        .deliverableView{
            overflow-x: scroll;
        }
        .projectTable, .projectTable-tbody, .projectTable-tr {
            display:table;
            width: 2000px;
            table-layout:fixed;
        }
        .projectTable-thead {
            width: calc( 2000px)
        }
        .project-sub-Table, .project-sub-Table-tbody, .project-sub-Table-tr {
            display:table;
            width: 1640px;
            table-layout:fixed;
        }
        .project-task-Table, .project-task-Table-tbody, .project-task-Table-tr {
            display:table;
            width: 1100px;
            table-layout:fixed;
        }
        td, th {
            border-left: 1px solid #c1d7e3;
        }
        td:first-child, th:first-child{
            border-left: none;
        }
        #contextMenu .item {
            cursor: pointer;
            transition: 1s;
        }
        #contextMenu .item:hover{
            background: #fff5f4;
            transition: 1s;
        }
        .panel-body{
            max-height: 85vh;
            padding: 10px;
        }
    </style>
@endsection
@section('main-content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                    </li>
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active">{{__($title)}}</li>

                    <li class="top-nav-btn">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white excelOut" data-name="{{ $project->name }}" data-toggle="tooltip" title="Excel Out"> <i class="las la-border-all">{{ __('Excel Out') }}</i></a>

                        <a href="{{ route('my_project.grid') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project Grid View"> <i class="las la-border-all">{{ __('Grid View') }}</i></a>

                        <a href="{{ route('my_project.my-project.index') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project List View"> <i class="las la-list-ul">{{ __('List View') }}</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info deliverableView">
                        <div class="panel-body">
                            <table class="table projectTable">
                                <thead class="projectTable-thead">
                                <tr>
                                    <th width="60px">SL.</th>
                                    <th width="200px">Deliverables</th>
                                    <th width="100px">Weightage</th>
                                    <th width="240px">Sub Deliverables</th>
                                    <th width="100px">Weightage</th>
                                    <th width="200px">Lead Department</th>
                                    <th width="200px">Description of Works</th>
                                    <th width="100px">Weightage</th>
                                    <th width="200px">Responsible Department</th>
                                    <th width="60px">Hour</th>
                                    <th width="100px">Initiate TimeLine</th>
                                    <th width="100px">End Timeline</th>
                                    <th width="200px">Remarks</th>
                                    {{--                                    @can('project-manage')--}}
                                    <th width="140px">Action</th>
                                    {{--                                    @endcan--}}
                                </tr>
                                </thead>
                                <tbody id="mainViewContent" data-action="{{ route('my_project.detail',$project->id) }}" class="projectTable-tbody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="projectActionModal" tabindex="-1" role="dialog" aria-labelledby="projectActionModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        const exportReportToExcel = (filename = '') => {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            let tableSelect = document.querySelector(".projectTable");
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename?filename+'.xls':'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob( blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
        document.querySelector(".excelOut").onclick = () => {
            exportReportToExcel(document.querySelector(".excelOut").getAttribute("data-name"))
        };
    </script>

    <script>
        (function ($){
            "use script";


            const runview = () => {
                $.ajax({
                    type: 'get',
                    url: $('#mainViewContent').attr('data-action'),
                    success:function (data){
                        $('#mainViewContent').empty().append(data)
                        runaction();
                    },
                    complete:function (xml){
                        if(!xml.status === 200){
                            alert('An error acquired');
                        }
                    }
                })

            }

            const runaction = () => {
                catchMouseEvent();
                taskAction();

                $('.subDeliverablesAddBtn').on('click', function (){
                    $.ajax({
                        type: 'get',
                        url: $(this).attr('data-action'),
                        success:function (data){
                            $('#projectActionModal').find('.modal-content').empty().append(data);
                            $('#projectActionModal').modal('show');
                            $('#projectActionModal form select').select2();
                            $('#projectActionModal form button[type="submit"]').on('click', formSubmit)
                            weightageValidation(document.querySelector('#projectActionModal input[name="weightage"]').getAttribute('max'))
                        },
                        complete(xml){
                            // console.log(xml)
                        }
                    })
                })

                $('.taskAddBtn').on('click', function (){
                    $.ajax({
                        type: 'get',
                        url: $(this).attr('data-action'),
                        success:function (data){
                            $('#projectActionModal').find('.modal-content').empty().append(data);
                            $('#projectActionModal').modal('show');
                            $('#projectActionModal form select').select2();
                            $('#projectActionModal form button[type="submit"]').on('click', formSubmit)
                            responsibleUser();
                            weightageValidation(document.querySelector('#projectActionModal input[name="weightage"]').getAttribute('max'))
                        },
                        complete(xml){
                            // console.log(xml)
                        }
                    })
                })
            }

            const responsibleUser = () => {
                const selectedDepartment = document.querySelector('select[name="department[]"]');
                const selectedUser = document.querySelector('select[name="user"]');

                selectedDepartment.onchange = (e) => {
                    if (e.target.getAttribute('data-action')) {
                        getAllUser(e.target.getAttribute('data-action'), e.target.value)
                    }
                }

                if(selectedUser && selectedDepartment.getAttribute('data-action')){
                    getAllUser(selectedDepartment.getAttribute('data-action'), selectedDepartment.value)
                }
            }

            const getAllUser = (url, value) => {
                const users = document.querySelector('select[name="user"]');
                $.ajax({
                    type: 'get',
                    url: `${url}/${value}`,
                    success: (data) => {
                        console.log(data)
                        users.innerHTML = '';
                        if (data.status === 200) {
                            let option = document.createElement('option');
                            let selectedUser = users.getAttribute('data-role')
                            option.value = null;
                            option.innerHTML = 'Select One';
                            users.appendChild(option);
                            Array.from(data.data.employees).map(item => {
                                let option = document.createElement('option')
                                option.value = item.id;
                                option.innerHTML = item.name;
                                if (selectedUser) {
                                    if (item.id === parseInt(selectedUser)) {
                                        option.selected = true;
                                    }
                                }
                                users.appendChild(option);
                            })
                        }
                    }
                })
            }

            const formSubmit = (e) => {
                e.preventDefault();
                const form = e.target.parentElement.parentElement;
                let inputs = form.querySelectorAll('input');
                let selects = form.querySelectorAll('select');
                let postData = []
                let errorData = []
                Array.from(inputs).map(item => {
                    if(item.getAttribute('name')) postData.push({
                        name: item.getAttribute('name'),
                        value: item.value
                    })
                })

                if($('#projectActionModal form select[name="department[]"]')) {
                    postData.push({
                        name: 'department',
                        value: $('#projectActionModal form select[name="department[]"]').select2().val()
                    })
                }

                if($('#projectActionModal form select[name="user"]')[0] != undefined) {
                    postData.push({
                        name: 'user',
                        value: $('#projectActionModal form select[name="user"]').select2().val()
                    })
                }

                if (form.querySelector('textarea')){
                    postData.push({
                        name: form.querySelector('textarea').getAttribute('name'),
                        value: form.querySelector('textarea').value
                    })
                }

                Array.from(postData).map(item => {
                    if(!item.value){
                        errorData.push({
                            name: item.name,
                            message:`${item.name} field is required`
                        })
                    }

                    if(Array.isArray(item.value)){
                        if(item.value.length < 1){
                            errorData.push({
                                name: item.name,
                                message:`${item.name} field is required`
                            })
                        }
                    }
                })
                // console.log(postData)
                if(errorData.length > 0){
                    Array.from(errorData).map(item => {
                        // notify(, 'warning')
                        toastr.warning(item.message);
                    })
                    return false;
                }
                $.ajax({
                    type: form.getAttribute('method'),
                    url: form.getAttribute('action'),
                    data: postData,
                    success:function (data){
                        console.log(data)
                        runview()
                    },
                    complete:function (xml){
                        // console.log(xml)
                        if (xml.status === 200){
                            notify('Saved Successfully', 'success')
                            $('#projectActionModal').modal('hide');
                        }else {
                            notify('Error', 'error')
                        }
                    }
                })
            }

            const catchMouseEvent = () => {
                let dataRow = document.querySelectorAll('.dataRow');
                Array.from(dataRow).map(item => {
                    item.onmousedown = function (e){
                        let mousex = event.clientX; // Gets Mouse X
                        let mousey = event.clientY; // Gets Mouse Y
                        // Prints data
                        let mouseEvent = window.event;
                        if(mouseEvent.which === 3){
                            e.preventDefault();
                            showCustomMenu({ x:mousex, y:mousey  }, e);
                        }else if(mouseEvent.which === 1){
                            $('#contextMenu').remove();
                        }
                    }
                })
            }

            const showCustomMenu = (position, e) => {
                document.querySelector('body').addEventListener("contextmenu", function (e){
                    e.preventDefault();
                }, false);
                $('#contextMenu').remove();
                let body = document.querySelector('body');
                body.style = 'position: relative;';

                let menu = document.createElement('div')
                menu.id = 'contextMenu';
                menu.style = `position: absolute; left: ${position.x}px; top: ${position.y}px; z-index: 1000000; width: 150px; background: #E5E5E5; border-radious: 5px;`
                let menuItemReload = document.createElement('div')
                menuItemReload.className = 'item cMReload p-2'
                menuItemReload.innerHTML = '<i class="las la-redo-alt" style="font-size: 16px;"></i>Reload'

                let menuItemEdit = document.createElement('div')
                menuItemEdit.className = 'item cMEdit p-2'
                menuItemEdit.innerHTML = '<i class="las la-edit" style="font-size: 16px;"></i>Edit'

                let menuItemDelete = document.createElement('div')
                menuItemDelete.className = 'item cMDelete p-2'
                menuItemDelete.innerHTML = '<i class="las la-trash" style="font-size: 16px;"></i>Delete'
                menu.appendChild(menuItemReload)
                menu.appendChild(menuItemEdit)
                menu.appendChild(menuItemDelete)
                body.appendChild(menu);

                let reloadBtn = document.querySelector('.cMReload');
                reloadBtn.onclick = reloadWindow;

                let editBtn = document.querySelector('.cMEdit');
                editBtn.onclick = function (){
                    editAndUpdate(e.target.closest('tr'))
                };

                let deleteBtn = document.querySelector('.cMDelete');
                deleteBtn.onclick = function (){
                    deleteRow(e.target.closest('tr'))
                };
            }

            const editAndUpdate = (item) => {
                if(!item.getAttribute('data-role')) {
                    $('#contextMenu').remove();
                    notify('You can\'t edit this item.', 'warning')
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: item.getAttribute('data-role'),
                    success:function (data){
                        $('#projectActionModal').find('.modal-content').empty().append(data.form);
                        $('#projectActionModal').modal('show');
                        $('#projectActionModal form select').select2().val(data.department_id).trigger('change');
                        $('#projectActionModal form button[type="submit"]').on('click', formSubmit);
                        responsibleUser();
                        weightageValidation(document.querySelector('#projectActionModal input[name="weightage"]').getAttribute('max'))
                    },
                    complete(xml){
                        // console.log(xml)
                    }
                })
                $('#contextMenu').remove();
            }

            const reloadWindow = () => {
                window.location.reload();
            }

            const deleteRow = (item) => {
                if(!item.getAttribute('data-action')) {
                    $('#contextMenu').remove();
                    notify('You can\'t delete this item.', 'warning')
                    return false;
                }
                $.ajax({
                    type: 'delete',
                    url: item.getAttribute('data-action'),
                    success:function (data){
                        // console.log(data)
                        notify(data, 'success')
                    },
                    complete:function (xml){
                        if(!xml.status === 200){
                            notify('Error', 'error')
                        }
                        runview();
                    }
                })
                $('#contextMenu').remove();
            }

            const taskAction = () => {
                Array.from(document.querySelectorAll('.taskActionBtn')).map(item => {
                    item.onchange = function (e) {
                        if(e.target.getAttribute('data-action')) {
                            $.ajax({
                                type: 'post',
                                url: e.target.getAttribute('data-action'),
                                data: {
                                    action: e.target.value
                                },
                                success: function (data) {
                                    if (data.status === 200) {
                                        notify(data.message, 'success');
                                    }else if(data.status === 400){
                                        notify(data.message, 'warning');
                                    }else if(data.status === 500){
                                        notify(data.message, 'error');
                                    }
                                    runview();
                                }
                            })
                        }
                    }
                })
            }

            const weightageValidation = (value) => {
                document.querySelector('#projectActionModal input[name="weightage"]').onkeyup = (e) => {
                    if(parseInt(e.target.value ) > parseInt(value)) {
                        notify(`Your Weightage already ${100 - value} Total Weightage value should not greater then 100%.`, 'warning');
                        document.querySelector('#projectActionModal button[type="submit"]').classList.add('d-none');
                        e.target.value = 0;
                    } else {
                        document.querySelector('#projectActionModal button[type="submit"]').classList.remove('d-none');
                    }
                }
            }
            runview();
        })(jQuery);
    </script>
@endsection
