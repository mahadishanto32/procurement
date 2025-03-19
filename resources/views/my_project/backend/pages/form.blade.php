@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
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
                        <a href="{{ route('my_project.my-project.index') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Project"> <i class="las la-list">{{ __('Project List') }}</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <form action="{{ $project?route('my_project.my-project.update', $project->id):route('my_project.my-project.store') }}" data-method="{{$project?'put':'post'}}" method="post" class="my-5 projectInputForm">
                                @csrf
                                @if($project)
                                    @method('PUT')
                                @endif

                                    <div class="col-12" id="formContents">
                                        @include('my_project.backend.pages.project-forms.step1')
                                        @include('my_project.backend.pages.project-forms.step2')
                                        @include('my_project.backend.pages.project-forms.step3')
                                    </div>
                                <!-- action buttons -->
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 float-left text-left my-3 d-none">
                                                <button data-role="0" type="button" class="btn btn-primary" id="backStepActionBtn">Back</button>
                                            </div>
                                            <div class="col-md-6 col-sm-6 float-right text-right my-3">
                                                <button data-role="0" type="button" class="btn btn-primary" id="nextStepActionBtn">Next</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        (function($){
            "use script";
            sessionStorage.clear();
            localStorage.removeItem('budget');
        const form = document.querySelector('.projectInputForm');
            if (form.getAttribute('data-method') === 'put'){
                $.ajax({
                    type: 'get',
                    url: $('select[name="department[]"]').attr('data-browse'),
                    success:function (data){
                        if(data.status === 200){
                            $('select[name="department[]"]').select2().val(data.value).trigger('change')
                        }
                    }
                })
            }

            const fromSections = document.querySelectorAll('.fromSection');
            let step = 1;

            const formSectionView = (value) => {
                let activesection = document.querySelector('.activeSection');
                if(!activesection) {
                    Array.from(fromSections).map((item, key) => {
                        if (key === 0) {
                            item.classList.add('activeSection');
                            item.classList.remove('d-none');
                        }
                    })
                }else if(value === 'next'){
                    for(key = 0; key < fromSections.length; key++){
                        let item = fromSections[key]
                        if(item.classList.contains('activeSection')){
                            item.classList.remove('activeSection');
                            item.classList.add('d-none');
                            let nextItemKey = key+ 1;
                            fromSections[nextItemKey].classList.add('activeSection');
                            fromSections[nextItemKey].classList.remove('d-none');
                            break;
                        }
                    }
                }else if(value === 'back'){
                    for(key = 0; key < fromSections.length; key++){
                        let item = fromSections[key]
                        if(item.classList.contains('activeSection')){
                            item.classList.remove('activeSection');
                            item.classList.add('d-none');
                            let backItemKey = key - 1;
                            fromSections[backItemKey].classList.add('activeSection');
                            fromSections[backItemKey].classList.remove('d-none');
                            break;
                        }
                    }
                }
            }

            document.querySelector('#nextStepActionBtn').onclick = (e) => {
                if(formValidation() === 200) {
                    step++
                    if (step >= fromSections.length) {
                        e.target.parentElement.classList.add('d-none');
                    }
                    document.querySelector('#backStepActionBtn').parentElement.classList.remove('d-none');
                    formSectionView('next')
                }else {
                    return;
                }
            };

            document.querySelector('#backStepActionBtn').onclick = (e) => {
                step--
                if (step === 1){
                    e.target.parentElement.classList.add('d-none');
                }
                document.querySelector('#nextStepActionBtn').parentElement.classList.remove('d-none');
                formSectionView('back')
            };

            const removeDeliverables = () => {
                let removeBtn = document.querySelectorAll('.deliverablesRemoveBtn');
                Array.from(removeBtn).map(item => {
                    item.onclick = (e) => {
                        e.target.parentElement.parentElement.remove()
                    }
                })
            }

            document.querySelector('.deliverablesAddBtn').onclick = () => {
                $('.deliverablesContainer').append('<tr>\n'+
                    '<td>\n'+
                    '<input type="hidden" name="d_id[]" />\n'+
                    '<input type="text" name="deliverables_name[]" class="form-control deliverablesName" placeholder="Deliverables Name" />\n'+
                    '</td>\n'+
                '<td>\n'+
                    '<input type="number" name="weightage[]" class="form-control deliverablesWeightage" placeholder="Weightage" />\n'+
                '</td>\n'+
                '<td>\n'+
                    '<input type="date" name="d_start_date[]" class="form-control" required/>\n'+
                '</td>\n'+
                '<td>\n'+
                    '<input type="date" name="d_end_date[]" class="form-control" required/>\n'+
                '</td>\n'+
                '<td>\n'+
                    '<input type="number" name="d_budget[]" id="dBudget" class="form-control" required/>\n'+
                '</td>\n'+
                '<td>\n'+
                    '<button type="button" class="btn btn-danger btn-sm deliverablesRemoveBtn">&times;</button>\n'+
                '</td>\n'+
            '</tr>');
                removeDeliverables();
                deliverablesTimelineValidator();
                deliverablesBudgetValidator();
                deliverablesWeightageValidator();
            }

            const formValidation = () => {
                let functionStatus = 200;
                let validationArray = [];
                const inputContainer = document.querySelector('.activeSection');
                let inputFields = Object.keys(inputContainer.querySelectorAll('input')).map(k => inputContainer.querySelectorAll('input')[k]);
                let textareas = Object.keys(inputContainer.querySelectorAll('textarea')).map(k=>inputContainer.querySelectorAll('textarea')[k]);
                let selects = Object.keys(inputContainer.querySelectorAll('select')).map(k => inputContainer.querySelectorAll('select')[k]);
                inputFields.push(...textareas);
                inputFields.push(...selects);
                Object.keys(inputContainer.querySelectorAll('.errorMessage')).map(k => {
                    inputContainer.querySelectorAll('.errorMessage')[k].remove()
                });
                Array.from(inputFields).map(item => {
                    if (item.getAttribute('required')){
                        if(!item.value) {
                            let spanElement = document.createElement("p")
                            spanElement.className = "text-danger errorMessage text-capitalize"
                            spanElement.innerText = `${item.getAttribute('name').replace('[]','')} is required`
                            item.parentElement.appendChild(spanElement);
                            validationArray.push(400)
                        }

                        if (item.type === "date") sessionStorage.setItem(item.getAttribute('name'),item.value);

                        if (item.name === 'budget') localStorage .setItem(item.getAttribute('name'),item.value);
                    }
                })

                for (let i = 0; i < validationArray.length; i++){
                    if(validationArray[i] === 400){
                        functionStatus = 400;
                        break
                    }
                }
                return functionStatus;
            }

            Array.from(document.querySelectorAll('.deliverablesRemoveBtnFromServer')).map(item => {
                item.addEventListener('click', function (e) {
                    swal({
                        title: "{{__('Are you sure?')}}",
                        text: "{{__('Once you delete, You can not recover this data and related files.')}}",
                        icon: "warning",
                        dangerMode: true,
                        buttons: {
                            cancel: true,
                            confirm: {
                                text: "Delete",
                                value: true,
                                visible: true,
                                closeModal: true
                            },
                        },
                    }).then((value) => {
                        if(value){
                            $.ajax({
                                type: 'delete',
                                url: e.target.getAttribute('data-role'),
                                success:function (data){
                                    if (data.status === 200){
                                        notify(data.message, 'success')
                                        e.target.closest('tr').remove();
                                    }
                                }
                            })
                        }
                    });
                })
            });

            const deliverablesTimelineValidator = () => {
              let deliverablesDateInputs = document.querySelector('#projectDeliverables').querySelectorAll('input[type="date"]');
              let timeLine = Object.keys(sessionStorage).map(item => {
                  return sessionStorage.getItem(item)
              })
                if(timeLine.length === 2) {
                    Array.from(deliverablesDateInputs).map((inputs, key) => {
                        inputs.setAttribute('min', timeLine[0])
                        inputs.setAttribute('max', timeLine[1])
                        inputs.value = inputs.value?inputs.value:timeLine[0];
                    })
                }else{
                    if (document.querySelector('#projectDeliverables').classList.contains('activeSection')) {
                        setTimeout(notify('Project Details form is not filed properly.', 'warning'), 2000);
                        formSectionView('back')
                    }
                }
            }

            const deliverablesBudgetValidator = () => {
                let deliverableBudgets = document.querySelectorAll('input[name="d_budget[]"]')
                Array.from(deliverableBudgets).map(item => {
                    item.onkeyup = (e) => {
                        let projectFullBudget = localStorage.getItem('budget');
                        let total = 0
                        Array.from(deliverableBudgets).map(sumItem => {
                            total += parseInt(sumItem.value?sumItem.value:0);
                        })
                        console.log(projectFullBudget)
                        console.log(total)

                        if(total > parseInt(projectFullBudget)){
                            notify('Deliverables budget should not grater then Project\'s budget.', 'warning');
                            document.querySelector('button[type="submit"]').classList.add('d-none');
                            e.target.value = 0;
                        } else {
                            document.querySelector('button[type="submit"]').classList.remove('d-none');
                        }
                    }
                })
            }
            
            const deliverablesWeightageValidator = () => {
                let deliverableWeightages = document.querySelectorAll('input[name="weightage[]"]')
                Array.from(deliverableWeightages).map(item => {
                    item.onkeyup = (e) => {
                        let projectWeightage = 100;
                        let total = 0
                        Array.from(deliverableWeightages).map(sumItem => {
                            total += parseInt(sumItem.value?sumItem.value:0);
                        })

                        if(total > parseInt(projectWeightage)){
                            notify('Total Weightage value should not greater then 100%.', 'warning');
                            document.querySelector('button[type="submit"]').classList.add('d-none');
                            e.target.value = 0;
                        } else {
                            document.querySelector('button[type="submit"]').classList.remove('d-none');
                        }
                    }
                })
            }

            const deliverablesBudgetBellowValidator = () => {
                let deliverableWeightages = document.querySelectorAll('input[name="weightage[]"]')
                let projectWeightage = 100;
                let total = 0
                Array.from(deliverableWeightages).map(item => {
                    total += parseInt(item.value?item.value:0);
                })

                if (total !== parseInt(projectWeightage)){
                    setTimeout(notify('Total Weightage value is smaller then 100%.', 'warning'),20000)
                    form.submit()
                }else {
                    form.submit()
                }
            }

            form.onsubmit = (e) => {
                e.preventDefault();
                deliverablesBudgetBellowValidator()
            }

            removeDeliverables();
            formSectionView();
            deliverablesTimelineValidator();
            deliverablesBudgetValidator();
            deliverablesWeightageValidator();
        })(jQuery);
    </script>
@endsection
