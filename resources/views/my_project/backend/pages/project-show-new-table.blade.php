@foreach($project->deliverables as $key => $deliverable)
    <tr class="projectTable-tr dataRow">
        <td width="3%">{{ $key+1 }}</td>
        <td width="10%">
            <span class="float-left">{{ $deliverable->name }}</span>
            {!! auth()->user()->hasPermissionTo('project-manage')?'<span class="float-right"><a href="javascript:void(0)" class="subDeliverablesAddBtn" data-action="'.route('my_project.modal-show',$deliverable->id).'"><i class="las la-plus-circle" style="font-size:  24px"></i></a></span>':(auth()->user()->hasRole('Super Admin')?'<span class="float-right"><a href="javascript:void(0)" class="subDeliverablesAddBtn" data-action="'.route('my_project.modal-show',$deliverable->id).'"><i class="las la-plus-circle" style="font-size:  24px"></i></a></span>':'') !!}
        </td>
        <td width="5%">
            {{ $deliverable->weightage }}
        </td>
        <td width="82%" class="p-0">
            <table class="project-sub-Table" border="0"
                   cellpadding="0"
                   cellspacing="0">
                @foreach($deliverable->subDeliverables as $subDeliverable)
                    <tr class="project-sub-Table-tr dataRow" {!! auth()->user()->hasPermissionTo('project-manage')?'data-role="'.route('my_project.sub-deliverable.show', $subDeliverable->id).'" data-action="'.route('my_project.sub-deliverable.destroy', $subDeliverable->id).'"':(auth()->user()->hasRole('Super Admin')?'data-role="'.route('my_project.sub-deliverable.show', $subDeliverable->id).'" data-action="'.route('my_project.sub-deliverable.destroy', $subDeliverable->id).'"':'') !!}>
                        <td width="12%">
                            <span class="float-left">{{ $subDeliverable->name }}</span>
                            {!! auth()->user()->hasPermissionTo('project-manage')?'<span class="float-right"><a href="javascript:void(0)" class="taskAddBtn" data-action="'.route('my_project.modal-show-task',$subDeliverable->id).'"><i class="las la-plus-circle" style="font-size:  24px"></i></a></span>':(auth()->user()->hasRole('Super Admin')?'<span class="float-right"><a href="javascript:void(0)" class="taskAddBtn" data-action="'.route('my_project.modal-show-task',$subDeliverable->id).'"><i class="las la-plus-circle" style="font-size:  24px"></i></a></span>':'') !!}

                        </td>
                        <td width="5%">{{ $subDeliverable->weightage }}</td>
                        <td width="10%">
                            @foreach($subDeliverable->departments as $department)
                                <p class="badge badge-primary">{{ $department->hr_department_name }}</p>
                            @endforeach
                        </td>
                        <td width="55%" class="p-0">
                            <table class="project-task-Table" border="0"
                                   cellpadding="0"
                                   cellspacing="0">
                                @foreach($subDeliverable->projectTasks as $projectTask)
                                    <tr class="project-task-Table-tr dataRow" {!! auth()->user()->hasPermissionTo('project-manage')?'data-role="'.route('my_project.task.show', $projectTask->id).'" data-action="'.route('my_project.task.destroy', $projectTask->id).'"':(auth()->user()->hasRole('Super Admin')?'data-role="'.route('my_project.task.show', $projectTask->id).'" data-action="'.route('my_project.task.destroy', $projectTask->id).'"':'') !!} >
                                        <td width="10%">{{ $projectTask->name }}</td>
                                        <td width="5%">{{ $projectTask->weightage }}</td>
                                        <td width="10%">
                                            @foreach($projectTask->departments as $department)
                                                <p class="badge badge-primary">{{ $department->hr_department_name }}</p>
                                            @endforeach
                                        </td>
                                        <td width="3%">{{ $projectTask->hour }}</td>
                                        <td width="5%">{{ date('d-M-y', strtotime($projectTask->initiate_time_line)) }}</td>
                                        <td width="5%">{{ date('d-M-y', strtotime($projectTask->end_time_line)) }}</td>
                                        <td width="10%">{{ $projectTask->remarks }}</td>
                                        <td width="7%">
                                            <div class="form-group">
                                                @if($projectTask->status ==='done')
                                                    <p class="font-weight-bold text-success text-center">Done</p>
                                                @else
                                                    @if(auth()->user()->hasPermissionTo('project-manage'))
                                                        <select data-action="{{ route('my_project.insert-action',$projectTask->id) }}"  class="form-control taskActionBtn">

                                                            <option {{ $projectTask->status === 'processing'?'selected':'' }} value="processing">Processing</option>
                                                            <option {{ $projectTask->status === 'pending'?'selected':'' }} value="pending">Pending</option>
                                                            <option {{ $projectTask->status === 'done'?'selected':'' }} value="done">Done</option>
                                                        </select>
                                                    @elseif(!auth()->user()->hasRole('Super Admin'))
                                                        {{--                                                    $projectTask->user_id === auth()->user()->id--}}
                                                        @if($projectTask->user_id == auth()->user()->id)
                                                            <select data-action="{{ route('my_project.insert-action',$projectTask->id) }}"  class="form-control taskActionBtn">

                                                                <option {{ $projectTask->status === 'processing'?'selected':'' }} value="processing">Processing</option>
                                                                <option {{ $projectTask->status === 'pending'?'selected':'' }} value="pending">Pending</option>
                                                                <option {{ $projectTask->status === 'done'?'selected':'' }} value="done">Done</option>
                                                            </select>
                                                        @endif
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
@endforeach