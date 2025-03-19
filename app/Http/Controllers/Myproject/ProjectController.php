<?php

namespace App\Http\Controllers\Myproject;

use App\Http\Controllers\Controller;
use App\Models\Hr\Department;
use App\Models\Hr\Unit;
use App\Models\MyProject\Deliverables;
use App\Models\MyProject\Holiday;
use App\Models\MyProject\Project;
use App\Models\MyProject\ProjectPage;
use App\Models\MyProject\ProjectTask;
use App\Models\MyProject\SubDeliverables;
use App\Models\MyProject\WeeklyStatus;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\This;
use Spatie\Permission\Models\Permission;

class ProjectController extends Controller
{
    public function pmo()
    {

        try {
            $title='Projects Management office';
            $departments = Department::all();
            $user = User::permission('project-manage')->first();
            $selectedDepartment = $user?$user->employee->department:null;
            return view('my_project.backend.pages.pmo',compact('title', 'departments', 'selectedDepartment'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function getUserAsDepartment($id)
    {
        try {
            $departmentId = $id;
            $user = User::permission('project-manage')->first();

            $employees=User::whereHas('employee',function($query) use($departmentId){
                return $query->where('as_department_id',$departmentId);
            })->get();
            return response()->json((object)['status' => 200, 'data'=> (object)[
                'employees' => $employees,
                'user' => $user,
            ]]);
        }catch (\Throwable $th)
        {
            return response()->json((object)['status' => 500, 'message'=>$th->getMessage()]);
        }
    }

    public function assignUserWithPermission(Request $request)
    {
        try {
            User::permission('project-manage')->each(function ($person){
                $person->syncPermissions(['']);
            });
            $user = User::find($request->user);
            $user->syncPermissions(['project-manage']);
            return response()->json((object)['status' => 200,'message'=>'User has been assigned successfully.']);
        }catch (\Throwable $th){
            return response()->json((object)['status' => 500, 'message'=>$th->getMessage()]);
        }
    }

    public function index()
    {
        if (!Auth::user()->hasAnyRole(['Super Admin', 'Management'])){
            return redirect()->route('my_project.grid');
        }
        try {
            $title='My Projects';
            $projects = Project::orderBy('id','DESC')
                ->where("status", "pending")
                ->paginate(20);
            return view('my_project.backend.pages.index',compact('title','projects'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function grid()
    {
        try {
            $title='My Projects';
            $projects = Project::orderBy('id','DESC')
                ->where("status", "approved")
                ->paginate(20);
            return view('my_project.backend.pages.index-grid',compact('title','projects'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function projectStatus($id)
    {
        $project = Project::findOrFail($id);
        if (Auth::user()->hasRole('Super Admin')){
            // do nothing
        } else if (!Auth::user()->hasAnyPermission(['project-manage', 'pmo', 'quotations-approval-list'])){
            return redirect()->route('my_project.my-project.show',$project->id);
        }
        try {
            $title = $project->name.' '.'Status';
            $deliverables = $project->deliverables;
            return view('my_project.backend.pages.project-status',compact('title','project', 'deliverables'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        try {
            $title='Project Create';
            $project = null;
            $departments = Department::all();
            $teams = Unit::where('hr_unit_status', true)->get();
            return view('my_project.backend.pages.form',compact('title', 'project', 'departments', 'teams'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'indent_no' => ['required', 'unique:projects'],
            'name' => ['required'],
            'work_location' => ['required'],
            'work_reason' => ['required'],
            'details' => ['required'],
            'type' => ['required'],
//            'teams' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'budget' => ['required'],
            'department' => ['required', 'array', 'min:1'],
            'deliverables_name' => ['required', 'array', 'min:1'],
            'weightage' => ['required', 'array', 'min:1'],
            'd_start_date' => ['required', 'array', 'min:1'],
            'd_end_date' => ['required', 'array', 'min:1'],
            'd_budget' => ['required', 'array', 'min:1']
        ]);
        try {
            $project = new Project();
            $project->indent_no = $request->indent_no;
            $project->name = $request->name;
            $project->work_location = $request->work_location;
            $project->work_reason = $request->work_reason;
            $project->details = $request->details;
            $project->sponsors = $request->sponsors;
            $project->terms = $request->terms;
            $project->risk = $request->risk;
            $project->items_dimension = $request->items_dimension;
            $project->status = !$request->status?'pending':$request->status;
            $project->type = $request->type;
            $project->budget = $request->budget;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->approved_by = $request->status?Auth::user()->id:null;
            $project->save();

            foreach ($request->deliverables_name as $key => $item){
                $deliverable = new Deliverables();
                $deliverable->project_id = $project->id;
                $deliverable->name = $request->deliverables_name[$key];
                $deliverable->weightage = $request->weightage[$key];
                $deliverable->start_at = $request->d_start_date[$key];
                $deliverable->end_at = $request->d_end_date[$key];
                $deliverable->budget = $request->d_budget[$key];
                $deliverable->save();
            }

            $project->departments()->sync($request->department);

            $notification = [
                'message' => 'Project Created Successfully.',
                'alert-type' => 'success'
            ];
            return redirect()->route('my_project.my-project.show',$project->id)->with($notification);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        try {
            $title='View Project '.$project->name;
            return view('my_project.backend.pages.project-show',compact('title', 'project'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        try {
            $title='Project Create';
            $departments = Department::all();
            $teams = Unit::where('hr_unit_status', true)->get();
            return view('my_project.backend.pages.form',compact('title', 'project', 'departments', 'teams'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function projectDepartment(Project $project)
    {
        try {
            $departmentIds = [];
            foreach ($project->departments as $item){
                $departmentIds[] = $item->hr_department_id;
            }
            return response()->json((object)['status'=>200, 'value'=>$departmentIds]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->validate($request, [
            'indent_no' => ['required'],
            'name' => ['required'],
            'work_location' => ['required'],
            'work_reason' => ['required'],
            'details' => ['required'],
            'type' => ['required'],
//            'teams' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'budget' => ['required'],
            'department' => ['required', 'array', 'min:1'],
            'deliverables_name' => ['required', 'array', 'min:1'],
            'weightage' => ['required', 'array', 'min:1'],
            'd_start_date' => ['required', 'array', 'min:1'],
            'd_end_date' => ['required', 'array', 'min:1'],
            'd_budget' => ['required', 'array', 'min:1'],
        ]);
        try {
            $project->indent_no = $request->indent_no;
            $project->name = $request->name;
            $project->work_location = $request->work_location;
            $project->work_reason = $request->work_reason;
            $project->details = $request->details;
            $project->sponsors = $request->sponsors;
            $project->terms = $request->terms;
            $project->risk = $request->risk;
            $project->items_dimension = $request->items_dimension;
//            $project->status = !$request->status?'pending':$request->status;
            $project->type = $request->type;
            $project->budget = $request->budget;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->approved_by = $request->status?Auth::user()->id:null;
            $project->save();

            foreach ($request->deliverables_name as $key => $item){
                if ($request->d_id[$key]){
                    $deliverable = Deliverables::findOrFail($request->d_id[$key]);
                    $deliverable->project_id = $project->id;
                    $deliverable->name = $request->deliverables_name[$key];
                    $deliverable->weightage = $request->weightage[$key];
                    $deliverable->start_at = $request->d_start_date[$key];
                    $deliverable->end_at = $request->d_end_date[$key];
                    $deliverable->budget = $request->d_budget[$key];
                    $deliverable->save();
                }else {
                    $deliverable = new Deliverables();
                    $deliverable->project_id = $project->id;
                    $deliverable->name = $request->deliverables_name[$key];
                    $deliverable->weightage = $request->weightage[$key];
                    $deliverable->start_at = $request->d_start_date[$key];
                    $deliverable->end_at = $request->d_end_date[$key];
                    $deliverable->budget = $request->d_budget[$key];
                    $deliverable->save();
                }
            }
            $project->departments()->sync($request->department);

            $notification = [
                'message' => 'Project Update Successfully.',
                'alert-type' => 'success'
            ];
            return redirect()->route('my_project.my-project.show',$project->id)->with($notification);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        try {
            $project->deliverables->each(function ($deliverable){
                $deliverable->subDeliverables->each(function ($subDeliverable){
                    $subDeliverable->departments()->sync([]);
                    $subDeliverable->projectTasks->each(function ($task){
                        $task->departments()->sync([]);
                        $task->delete();
                    });
                    $subDeliverable->delete();
                });
                $deliverable->delete();
            });
            $project->departments()->sync([]);
            $project->weeklyStatus->each->delete();
            $project->delete();
            return response()->json((object)['status'=>200, 'message'=> 'Project deleted successfully.', 'routeUrl'=>route('my_project.grid'),'p'=>$project]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function deliverableDestroy(Deliverables $deliverable)
    {
        try {
            return response()->json((object)['status'=>200, 'message'=>'Milestone successfully deleted.']);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function showProjectTable(Project $project)
    {
        try {
            return view('my_project.backend.pages.project-show-new-table',compact('project'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function subDeliverableModalShow(Deliverables $deliverable)
    {
        $departments = $deliverable->project->departments;
        $max = 100;
        $total = 0;
        if (count($deliverable->subDeliverables) > 0) {
            foreach ($deliverable->subDeliverables as $subDeliverable){
                $total += $subDeliverable->weightage;
            }
        }
        $options = ['<option value="'.null.'">Select Department</option>'];
        foreach ($departments as $department){
            $options[] = '<option value="'.$department->hr_department_id.'">'.$department->hr_department_name.'</option>';
        }


        try {
            $output = '<form action="'.route('my_project.sub-deliverable.store', $deliverable->id).'" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectActionModalLongTitle">Add New Child Milestone</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Child Milestone Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="weightage">Weightage</label>
                            <input type="number" name="weightage" id="weightage" class="form-control" max="'.($max - $total).'" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Lead Department</label>
                            <select name="department[]" id="department" class="form-control" multiple style="width: 100%!important;" required>'.
                                implode(',',$options)
                            .'</select>
                        </div>
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label for="startAt">Start Date</label>
                                <input type="date" name="start_at" id="startAt" class="form-control" required min="'.$deliverable->start_at.'" max="'.$deliverable->end_at.'" value="'.$deliverable->start_at.'"/>
                            </div>
                            <div class="col-6 form-group">
                                <label for="endAt">End Date</label>
                                <input type="date" name="end_at" id="endAt" class="form-control" required min="'.$deliverable->start_at.'" max="'.$deliverable->end_at.'" value="'.$deliverable->end_at.'"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>';
            return response()->json($output,200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(),406);
        }
    }

    public function storeSubDeliverable(Request $request, Deliverables $deliverable)
    {
        try {
            $subDeliverables = new SubDeliverables();
            $subDeliverables->deliverable_id = $deliverable->id;
            $subDeliverables->name = $request->name;
            $subDeliverables->weightage = $request->weightage;
            $subDeliverables->start_at = $request->start_at;
            $subDeliverables->end_at = $request->end_at;
            $subDeliverables->save();
            $subDeliverables->departments()->sync(explode(',', $request->department));
            return response()->json($request->all());
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function showSubDeliverable(SubDeliverables $subDeliverable)
    {
        $max = 100;
        $total = 0;
        if (count($subDeliverable->deliverable->subDeliverables) > 0) {
            foreach ($subDeliverable->deliverable->subDeliverables as $item){
                $total += $item->weightage;
            }
        }
        $total -= $subDeliverable->weightage;

        $departments = $subDeliverable->deliverable->project->departments;
        $options = ['<option value="'.null.'">Select Department</option>'];
        foreach ($departments as $department){
            $options[] = '<option value="'.$department->hr_department_id.'">'.$department->hr_department_name.'</option>';
        }

        try {
            $output = '<form action="'.route('my_project.sub-deliverable.update', $subDeliverable->id).'" method="put">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectActionModalLongTitle">Edit Child Milestone</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Child Milestone Name</label>
                            <input type="text" name="name" id="name" class="form-control" required value="'.$subDeliverable->name.'">
                        </div>
                        <div class="form-group">
                            <label for="weightage">Weightage</label>
                            <input type="number" name="weightage" id="weightage" class="form-control" required max="'.($max - $total).'" value="'.$subDeliverable->weightage.'">
                        </div>
                        <div class="form-group">
                            <label for="department">Lead Department</label>
                            <select name="department[]" id="department" class="form-control" multiple style="width: 100%!important;" required>'.
                implode(',',$options)
                .'</select>
                        </div>
                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label for="startAt">Start Date</label>
                                <input type="date" name="start_at" id="startAt" class="form-control" required min="'.$subDeliverable->deliverable->start_at.'" max="'.$subDeliverable->deliverable->end_at.'" value="'.$subDeliverable->start_at.'"/>
                            </div>
                            <div class="col-6 form-group">
                                <label for="endAt">End Date</label>
                                <input type="date" name="end_at" id="endAt" class="form-control" required min="'.$subDeliverable->deliverable->start_at.'" max="'.$subDeliverable->deliverable->end_at.'" value="'.$subDeliverable->end_at.'"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>';
            $departmentIds = [];
            foreach ($subDeliverable->departments as $dep){
                $departmentIds[] = $dep->hr_department_id;
            }
            return response()->json((object)[
                'form' => $output,
                'department_id' => $departmentIds
            ],200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(),406);
        }
    }

    public function updateSubDeliverable(Request $request, SubDeliverables $subDeliverable)
    {
        try {
            $subDeliverable->name = $request->name;
            $subDeliverable->weightage = $request->weightage;
            $subDeliverable->start_at = $request->start_at;
            $subDeliverable->end_at = $request->end_at;
            $subDeliverable->save();
            $subDeliverable->departments()->sync(explode(',', $request->department));
            return response()->json($request->all());
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function destroySubDeliverable(SubDeliverables $subDeliverable)
    {
        try {
            $subDeliverable->departments()->sync([]);
            $subDeliverable->projectTasks->each(function ($task){
                $task->departments()->sync([]);
                $task->delete();
            });
            $subDeliverable->delete();

            return response()->json('Successfully deleted', 200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(), 406);
        }
    }

    public function taskModalShow(SubDeliverables $subDeliverable)
    {
        $max = 100;
        $total = 0;
        if (count($subDeliverable->projectTasks) > 0) {
            foreach ($subDeliverable->projectTasks as $task){
                $total += $task->weightage;
            }
        }

        $departments = $subDeliverable->departments;
        $options = ['<option value="'.null.'">Select Department</option>'];
        foreach ($departments as $department){
            $options[] = '<option value="'.$department->hr_department_id.'">'.$department->hr_department_name.'</option>';
        }

        try {
            $output = '<form action="'.route('my_project.task.store', $subDeliverable->id).'" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectActionModalLongTitle">Add New Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-8">
                                <label for="name">Description of Works</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="form-group col-4">
                                <label for="weightage">Weightage</label>
                                <input type="number" name="weightage" id="weightage" class="form-control" required max="'.($max - $total).'">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="department">Responsible Department</label>
                                <select name="department[]" id="department" class="form-control" style="width: 100%!important;" required data-action="'.route('my_project.pmo').'">'.
                                    implode(',',$options)
                                .'</select>
                            </div>
                            <div class="form-group col-6">
                                <label for="user">Responsible User</label>
                                <select name="user" id="user" class="form-control" style="width: 100%" required data-role="0"></select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                        <div class="col-6 form-group">
                            <label for="initiateTimeLine">Initiate TimeLine</label>
                            <input type="date" min="'.$subDeliverable->start_at.'" max="'.$subDeliverable->end_at.'" name="initiate_time_line" id="initiateTimeLine" class="form-control" required value="'.$subDeliverable->start_at.'">
                        </div>
                        <div class="col-6 form-group">
                            <label for="endTimeLine">End Timeline</label>
                            <input type="date" min="'.$subDeliverable->start_at.'" max="'.$subDeliverable->end_at.'" name="end_time_line" id="endTimeLine" class="form-control" required value="'.$subDeliverable->end_at.'">
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remark</label>
                            <textarea name="remarks" id="remarks"class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>';
            return response()->json($output,200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(),406);
        }
    }

    public function storeTask(Request $request, SubDeliverables $subDeliverable)
    {
        $timestamp1 = strtotime($request->initiate_time_line);
        $timestamp2 = strtotime($request->end_time_line);
        $estimatedDays = abs($timestamp2 - $timestamp1)/(60*60);
        $user = User::find($request->user);
        try {
            $task = new ProjectTask();
            $task->sub_deliverable_id = $subDeliverable->id;
            $task->name = $request->name;
            $task->hour = $estimatedDays;
            $task->initiate_time_line = Carbon::parse($request->initiate_time_line);
            $task->end_time_line = Carbon::parse($request->end_time_line);
            $task->remarks = $request->remarks;
            $task->weightage = $request->weightage;
            $task->user_id = $request->user;
            $task->save();
            $task->departments()->sync(explode(',', $request->department));
            $user->givePermissionTo('project-action');
            return response()->json($request->all(), 200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(), 406);
        }
    }

    public function showTask(ProjectTask $task)
    {
        $max = 100;
        $total = 0;
        if (count($task->subDeliverable->projectTasks) > 0) {
            foreach ($task->subDeliverable->projectTasks as $item){
                $total += $item->weightage;
            }
        }
        $total -= $task->weightage;

        $departments = $task->subDeliverable->departments;
        $options = ['<option value="'.null.'">Select Department</option>'];
        foreach ($departments as $department){
            $options[] = '<option value="'.$department->hr_department_id.'">'.$department->hr_department_name.'</option>';
        }

        try {
            $output = '<form action="'.route('my_project.task.update', $task->id).'" method="put">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectActionModalLongTitle">Edit '.$task->name.'</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-8">
                                <label for="name">Description of Works</label>
                                <input type="text" name="name" id="name" class="form-control" required value="'.$task->name.'">
                            </div>
                            <div class="form-group col-4">
                                <label for="weightage">Weightage</label>
                                <input type="number" name="weightage" id="weightage" class="form-control" max="'.($max - $total).'" required value="'.$task->weightage.'">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="department">Responsible Department</label>
                            <select name="department[]" id="department" class="form-control department" style="width: 100%!important;" required     data-action="'.route('my_project.pmo').'">'.
                                implode(',',$options)
                            .'</select>
                            </div>
                            <div class="form-group col-6">
                                <label for="user">Responsible User</label>
                                <select name="user" id="user" class="form-control" style="width: 100%" required data-role="'.($task->user?$task->user->id:'').'"></select>
                            </div>
                        </div>
                        <div class="form-row">
                        <div class="col-6 form-group">
                            <label for="initiateTimeLine">Initiate TimeLine</label>
                            <input type="date" min="'.$task->subDeliverable->start_at.'" max="'.$task->subDeliverable->end_at.'" name="initiate_time_line" id="initiateTimeLine" class="form-control" required value="'.date('Y-m-d',strtotime($task->initiate_time_line)).'">
                        </div>
                        <div class="col-6 form-group">
                            <label for="endTimeLine">End Timeline</label>
                            <input type="date" min="'.$task->subDeliverable->start_at.'" max="'.$task->subDeliverable->end_at.'" name="end_time_line" id="endTimeLine" class="form-control" required value="'.date('Y-m-d',strtotime($task->end_time_line)).'">
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remark</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3" required>'.$task->remarks.'</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>';
            $departmentIds = [];
            foreach ($task->departments as $dep){
                $departmentIds[] = $dep->hr_department_id;
            }
            return response()->json((object)[
                'form' => $output,
                'department_id' => $departmentIds
            ],200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(),406);
        }
    }

    public function updateTask(Request $request, ProjectTask $task)
    {
        $timestamp1 = strtotime($request->initiate_time_line);
        $timestamp2 = strtotime($request->end_time_line);
        $estimatedDays = abs($timestamp2 - $timestamp1)/(60*60);
        if($task->user){
            $task->user->revokePermissionTo('project-action');
        }
        $user = User::find($request->user);
        try {
            $task->name = $request->name;
            $task->hour = $estimatedDays;
            $task->initiate_time_line = Carbon::parse($request->initiate_time_line);
            $task->end_time_line = Carbon::parse($request->end_time_line);
            $task->remarks = $request->remarks;
            $task->weightage = $request->weightage;
            $task->user_id = $request->user;
            $task->save();
            $task->departments()->sync(explode(',', $request->department));
            $user->givePermissionTo('project-action');
            return response()->json($user, 200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(), 406);
        }
    }

    public function destroyTask(ProjectTask $task)
    {
        try {
            $task->departments()->sync([]);
            $task->delete();
            return response()->json('Successfully deleted', 200);
        }catch (\Throwable $th){
            return response()->json($th->getMessage(), 406);
        }
    }

    public function taskUpdateAction(Request $request, ProjectTask $task)
    {
        try {
            if ($task->subDeliverable->deliverable->project->status === 'approved') {
                if ($request->action === 'done') {
                    $task->status = $request->action;
                    $task->save();

                    $subDeliverable = $task->subDeliverable;
                    $subDeliverable->status_at = $subDeliverable->status_at + (($subDeliverable->weightage * $task->weightage) / 100);
                    $subDeliverable->save();

                    $deliverable = $subDeliverable->deliverable;
                    $deliverable->status_at = $deliverable->status_at + (($deliverable->weightage * $subDeliverable->status_at) / 100);
                    $deliverable->save();

                    $project = $deliverable->project;
                    $project->status_at = $project->status_at + ((100 * $deliverable->status_at) / 100);
                    $project->save();

                    $weeks = round((((time() - strtotime($project->start_date)) / (24*60*60))+1)/7);

                    $actionLog = new WeeklyStatus();
                    $actionLog->project_id = $project->id;
                    $actionLog->date = date('Y-m-d');
                    $actionLog->day = date('D');
                    $actionLog->status_at = $project->status_at;
                    $actionLog->week_no = $weeks;
                    $actionLog->save();
                } else {
                    $task->status = $request->action;
                    $task->save();
                }

                return response()->json((object)['status' => 200, 'message' => 'task updated successfully', 'data' => $task]);
            }elseif($task->subDeliverable->deliverable->project->status === 'pending') {
                return response()->json((object)['status' => 400, 'message' => 'Project is still pending', 'data' => null]);
            }else{
                return response()->json((object)['status' => 500, 'message' => 'Project is halt up.', 'data' => null]);
            }
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function projectUpdateAction(Request $request, Project $project)
    {
        try {
            $project->update(['status'=>$request->action]);
            return response()->json((object)[
                'status' => 200,
                'message' => 'Project updated successfully'
            ]);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function projectReport(Project $project)
    {
        try {
            $title= $project->name.' Report';
            $lastWeekProgress = WeeklyStatus::where('day','Thu')->orderBy('id','DESC')->first();
            return view('my_project.backend.pages.reports.index',compact('title','project','lastWeekProgress'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function ganttChart(Project $project)
    {
        try {
            $title= $project->name.' Gantt Chart';
            return view('my_project.backend.pages.gantt-chart.index',compact('title','project'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function ganttChartsDatas(Project $project)
    {
        try {
            $data = [];
            $module = [];
            foreach ($project->deliverables as $deliverable){
                foreach ($deliverable->subDeliverables as $subDeliverable){
                    foreach ($subDeliverable->projectTasks as $projectTask){
                        $color = $this->randomColor();

                        $data[] = (object)[
                            "category" => $deliverable->name. " ( ". $subDeliverable->name." )",
                            "start" => $projectTask->initiate_time_line,
                            "end" => $projectTask->end_time_line,
                            "color"=> $color,
                            "task" => $projectTask->name
                        ];
                    }
                }
            }
            return response()->json($data);
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function randomColor()
    {
        $random = "0." . rand();
        $x = floor( floatval($random) * 256);
        $y = 100+ floor(floatval($random) * 256);
        $z = 50+ floor(floatval($random) * 256);
        $bgColor = "rgb(" . $x . "," . $y . "," . $z . ")";
        $opColor = "rgb(" . $x . "," . $y . "," . $z .",". 0.5 .")";
//        return (object)[
//            'color'=>$bgColor,
//            'opacity'=>$opColor
//        ];
        return $bgColor;
    }

    public function statusWiseProjectChart()
    {
        try {
            $continuousProjects = Project::where("status", "approved")
                ->where('status_at', '<', 100)
                ->where('end_date', '>', date("Y-m-d", time()))
                ->get();
            $completedProjects = Project::where("status", "approved")
                ->where('status_at', '=', 100)
                ->get();
            $exceedProjects = Project::where("status", "approved")
                ->where('status_at', '<', 100)
                ->where('end_date', '<', date("Y-m-d", time()))
                ->get();
            return response()->json(compact('continuousProjects', 'completedProjects','exceedProjects'));
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
