<div class="col-md-8 col-sm-10 mx-auto fromSection d-none" id="projectDeclare">
    <u><h4 class="text-center">{{ __('Project Declare') }}</h4></u>
    <u><h6 class="text-center stepName">Step 1/3</h6></u>
    @php
        $prefix='PR-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
        $poRefNo=uniqueCode(14,$prefix,'projects','id');
    @endphp
    <div class="form-row">
        <div class="col-md-6">
            <label for="indentNo">Indent No <i class="las la-star text-danger"></i></label>
            <input type="text" name="indent_no" id="indentNo" class="form-control" required value="{{ $project?$project->indent_no:(old('indent_no')?old('indent_no'):$poRefNo) }}" readonly>
        </div>
        <div class="col-md-12">
            <label for="name">Name <i class="las la-star text-danger"></i></label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ $project?$project->name:old('name') }}">
        </div>
        <div class="col-md-6">
            <label for="workLocation">Work Location <i class="las la-star text-danger"></i></label>
            <input type="text" name="work_location" id="workLocation" class="form-control" required value="{{ $project?$project->work_location:old('work_location') }}">
        </div>
        <div class="col-md-6">
            <label for="workReason">Work Reason <i class="las la-star text-danger"></i></label>
            <input type="text" name="work_reason" id="workReason" class="form-control" required value="{{ $project?$project->work_reason:old('work_reason') }}">
        </div>
        <div class="col-md-12">
            <label for="details">In Scope <i class="las la-star text-danger"></i></label>
            <textarea name="details" id="details" class="form-control summernote" rows="10" required>{!! $project?$project->details:old('details') !!}</textarea>
        </div>
        <div class="col-md-6">
            <label for="itemsDimension">Items Dimension</label>
            <input type="text" name="items_dimension" id="itemsDimension" class="form-control" value="{{ $project?$project->items_dimension:old('items_dimension') }}">
        </div>
        <div class="col-md-6">
{{--            <label for="status">Status <i class="las la-star text-danger"></i></label>--}}
{{--            <select name="status" id="status" class="form-control" required>--}}
{{--                <option value="{{ null }}">Select One</option>--}}
{{--                <option {{ $project?($project->status === 'pending'?'selected':''):(old('status') === 'pending'?'selected':'' ) }} value="pending">Pending</option>--}}
{{--                <option {{ $project?($project->status === 'approved'?'selected':''):(old('status') === 'approved'?'selected':'' ) }} value="approved">Approved</option>--}}
{{--                <option {{ $project?($project->status === 'halt'?'selected':''):(old('status') === 'halt'?'selected':'' ) }} value="halt">Halt</option>--}}
{{--            </select>--}}
            <div class="col-md-12">
                <label for="itemsDimension">Project Total Budget (Value) <i class="las la-star text-danger"></i></label>
                <input type="number" name="budget" id="budget" class="form-control" value="{{ $project?$project->budget:'' }}" required>
            </div>
        </div>
        <div class="col-md-12">
            <label for="type">Type <i class="las la-star text-danger"></i></label>
            <select name="type" id="type" class="form-control" required>
                <option value="{{ null }}">Select One</option>
                <option {{ $project?($project->type === 'treading'?'selected':''):(old('type') === 'treading'?'selected':'' ) }} value="treading">Treading</option>
                <option {{ $project?($project->type === 'manufacture'?'selected':''):(old('type') === 'manufacture'?'selected':'' ) }} value="manufacture">Manufacture</option>
            </select>
        </div>

    </div>
</div>