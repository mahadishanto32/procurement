@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
    <link rel="stylesheet" href="{{ asset('plugins/plugin/evo-calendar/evo-calendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/plugin/evo-calendar/evo-calendar.royal-navy.min.css') }}">
    <style>
        .calendar-months li.month{
            color: #fff5f4;
        }
        /*.royal-navy .calendar-sidebar>span#sidebarToggler {*/
        /*    background-color: transparent;*/
        /*    box-shadow: none;*/
        /*}*/
        /*.royal-navy .calendar-sidebar>span#sidebarToggler button.icon-button{*/
        /*    background-color: #f7be16;*/
        /*}*/
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
                    <li class="active">{{__($title)}} </li>
                    <li class="top-nav-btn">
                        <a href="{{ route('my_project.grid') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project Grid View"> <i class="las la-border-all">{{ __('Grid View') }}</i></a>

                        <a href="{{ url('/pms/my-project/create') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Project"> <i class="las la-plus">Add</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addVacationModal" tabindex="-1" role="dialog" aria-labelledby="addVacationModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVacationModalLongTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('my_project.holiday-setup.store-holiday') }}" method="post" id="vacationForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="date">
                        <div class="form-group">
                            <label for="name">Name of Event:</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger destroyButton float-left">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('my_project.holiday-setup.destroy-holiday') }}" method="post" id="holidayDestroyForm">
        @csrf
        @method('delete')
        <input type="hidden" name="date">
    </form>
@endsection
@section('page-script')
    <script src="{{ asset('plugins/plugin/evo-calendar/evo-calendar.js') }}"></script>
    <script>
        (function ($){
           'use script';
            // calendarEvents:
            let allDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
            let allMonth = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            let now = new Date();
            let currentYear = now.getFullYear();
            let calendarYear = currentYear;
            let events =  [];

            setTimeout(() => {
                console.log(document.querySelectorAll('.icon-button'))
                Array.from(document.querySelectorAll('.icon-button')).map(item => {
                    item.onclick = (e) => {
                        if (e.target.parentElement.getAttribute('data-year-val') === 'prev'){
                            runCalendar();
                            calendarYear = document.querySelector('.calendar-year p').innerHTML;
                            calendarYear = parseInt(calendarYear);
                        } else if (e.target.parentElement.getAttribute('data-year-val') === 'next'){
                            runCalendar();
                            calendarYear = document.querySelector('.calendar-year p').innerHTML;
                            calendarYear = parseInt(calendarYear);
                        }
                    }

                })
            },3000)

            const runCalendar = () => {
                $.ajax({
                    type: 'get',
                    url: '/pms/holiday-setup/holidays',
                    success:function (data) {
                        events = [];
                        Array.from(data).map(item => {
                            let day =  item.date;
                            let month =  parseInt(item.month) - 1;
                            let year =  item.year;
                            let fullDate = `${allMonth[month]}/${day}/${year===null?calendarYear:year}`
                            let eventItem = {
                                id: item.id, // Event's ID (required)
                                name: item.name, // Event name (required)
                                date: fullDate, // Event date (required)
                                type: "holiday", // Event type (required)
                                everyYear: true // Same event every year (optional)
                            }
                            if(calendarYear === parseInt(year)){
                                events.push(eventItem);
                            } else if(!year) {
                                events.push(eventItem);
                            }
                        });
                        showCalendar()
                    }
                })
            }

            const showCalendar = () => {
                $('#calendar').evoCalendar({
                    theme: 'Royal Navy',
                    calendarEvents: events
                });
                Array.from(document.querySelectorAll('.month')).map(item => {
                    item.onclick = eventAdd;
                });
                eventAdd();
            }

            const eventAdd = () => {
                const calendar = document.querySelector('#calendar').querySelector('.calendar-inner');
                const calendarDate = calendar.querySelectorAll('.day');
                Array.from(calendarDate).map(item => {
                    item.addEventListener('dblclick', (e) => {
                        let clickDate = new Date(e.target.getAttribute('data-date-val'))
                        let day = allDays[clickDate.getDay()];
                        let date = clickDate.getDate();
                        let month = allMonth[clickDate.getMonth()];
                        let year = clickDate.getFullYear();

                        if(e.target.querySelector('.event-indicator')){
                            getEvent(e);
                        }else {
                            document.querySelector('#addVacationModalLongTitle').innerText = `Add Vacation at ${day}, ${date} ${month} ${year}`;
                            document.querySelector('#vacationForm input[name="date"]').value = e.target.getAttribute('data-date-val');
                            document.querySelector('#addVacationModal .modal-footer .destroyButton').classList.add('d-none')
                            document.querySelector('#vacationForm #name').value = '';
                            $('#addVacationModal').modal('show');
                        }
                    })
                });
            }

            const getEvent = (e) => {
               let form = e.target.parentElement;
               let date = e.target.getAttribute('data-date-val')
                $.ajax({
                    type: 'get',
                    url: `/pms/holiday-setup/holiday`,
                    data:{
                        holiday: date
                    },
                    success:function (data){
                        let clickDate = new Date(date)
                        let day = allDays[clickDate.getDay()];
                        let eDate = clickDate.getDate();
                        let month = allMonth[clickDate.getMonth()];
                        let year = clickDate.getFullYear();

                        document.querySelector('#addVacationModalLongTitle').innerText = `Edit Vacation at ${day}, ${eDate} ${month} ${year}`;
                        document.querySelector('#vacationForm input[name="date"]').value = date;
                        document.querySelector('#vacationForm #name').value = data.name;
                        document.querySelector('#addVacationModal .modal-footer .destroyButton').classList.remove('d-none')
                        $('#addVacationModal').modal('show');
                    }
                })
            }

            const destroyEvent = (e) => {
               let deleteForm = document.querySelector('#holidayDestroyForm');
               deleteForm.querySelector('input[name="date"]').value = e.target.parentElement.parentElement.querySelector('#vacationForm input[name="date"]').value
                deleteForm.submit();
            }

            document.querySelector('#addVacationModal .modal-footer .destroyButton').onclick = (e) => {
                swal({
                    title: "{{__('Are you sure?')}}",
                    text: 'Once you delete, You can not recover this data and related files.',
                    icon: "warning",
                    dangerMode: true,
                    buttons: {
                        cancel: true,
                        confirm: {
                            text: 'Delete',
                            value: true,
                            visible: true,
                            closeModal: true
                        },
                    },
                }).then((value) => {
                    if(value){
                        destroyEvent(e)
                    }
                });
            };

            runCalendar();
        })(jQuery);
    </script>
@endsection
