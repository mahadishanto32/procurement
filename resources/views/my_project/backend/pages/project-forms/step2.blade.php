<div class="col-md-8 col-sm-10 mx-auto fromSection d-none" id="projectDetails">
    <u><h4 class="text-center">{{ __('Project Details') }}</h4></u>
    <u><h6 class="text-center stepName">Step 2/3</h6></u>
    <div class="form-row">
        <div class="col-md-12">
            <label for="sponsors">Sponsors</label>
            <textarea name="sponsors" id="sponsors" class="form-control" rows="3">{!! $project?$project->sponsors:old('sponsors') !!}</textarea>
        </div>
        <div class="col-md-12">
            <label for="teams">Teams <i class="las la-star text-danger"></i></label>
            <select name="teams" id="teams" class="form-control" style="width: 100%" required>
                <option value="{{ null }}">Select One</option>
                @foreach($teams as $team)
                <option {{ $project?($project->teams === $team->hr_unit_id?'selected':'' ):(old('teams') === $team->hr_unit_id?'selected':'') }} value="{{ $team->hr_unit_id  }}">{{ $team->hr_unit_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <label for="terms">Terms / Projects / Contacts</label>
            <textarea name="terms" id="terms" class="form-control" rows="3">{!! $project?$project->terms:old('terms') !!}</textarea>
        </div>
        <div class="col-md-12">
            <label for="department">Key Stakeholder <i class="las la-star text-danger"></i></label>
            <select name="department[]" id="department" class="form-control" style="width: 100%" multiple required data-browse="{{ $project?route('my_project.departments',$project->id):'' }}">
                <option value="{{ null }}">Select One</option>
                @foreach($departments as $department)
                    <option value="{{ $department->hr_department_id  }}">{{ $department->hr_department_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="startDate">Start Date <i class="las la-star text-danger"></i></label>
            <input type="date" name="start_date" id="startDate" class="form-control" required {!! $project?'':'min="'.date('Y-m-d', time()).'"' !!} value="{{ $project?date('Y-m-d', strtotime($project->start_date)):(old('start_date')?old('start_date'):date('Y-m-d', time())) }}">
        </div>
        <div class="col-md-6">
            <label for="endDate">Indent No <i class="las la-star text-danger"></i></label>
            <input type="date" name="end_date" id="endDate" class="form-control" required {!! $project?'':'min="'.date('Y-m-d', time()).'"' !!} value="{{ $project?date('Y-m-d', strtotime($project->end_date)):(old('end_date')?old('end_date'):date('Y-m-d', (time()+(24*60*60)))) }}">
        </div>
        <div class="col-md-12">
            <label for="risk">Risk</label>
            <textarea name="risk" id="risk" class="form-control" rows="3">{!! $project?$project->risk:old('risk') !!}</textarea>
        </div>
    </div>
</div>