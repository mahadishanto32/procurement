<div class="col-md-10 col-sm-12 mx-auto fromSection d-none" id="projectDeliverables">
    <u><h4 class="text-center mb-2">{{ __('Create Deliverables') }}</h4></u>
    <u><h6 class="text-center stepName">Step 3/3</h6></u>
    <table class="table">
        <thead>
        <tr>
            <th width="35%">Deliverables Name <i class="las la-star text-danger"></i></th>
            <th width="10%">Weightage <i class="las la-star text-danger"></i></th>
            <th width="15%">Start At <i class="las la-star text-danger"></i></th>
            <th width="15%">End At <i class="las la-star text-danger"></i></th>
            <th width="15%">Budget <i class="las la-star text-danger"></i></th>
            <th width="10%">Action</th>
        </tr>
        </thead>
        <tbody class="deliverablesContainer">
        @if($project)
            @foreach($project->deliverables as $deliverable)
                <tr>
                    <td>
                        <input type="hidden" name="d_id[]" value="{{ $deliverable->id }}" />
                        <input type="text" name="deliverables_name[]" class="form-control deliverablesName" placeholder="Deliverables Name" required value="{{ $deliverable->name }}" />
                    </td>
                    <td>
                        <input type="number" name="weightage[]" class="form-control deliverablesWeightage" placeholder="Weightage" required value="{{ $deliverable->weightage }}" />
                    </td>
                    <td>
                        <input type="date" name="d_start_date[]" id="startDate" class="form-control" required value="{{ date('Y-m-d', strtotime($deliverable->start_at )) }}" />
                    </td>
                    <td>
                        <input type="date" name="d_end_date[]" id="endDate" class="form-control" required value="{{ date('Y-m-d', strtotime($deliverable->end_at)) }}" />
                    </td>
                    <td>
                        <input type="number" name="d_budget[]" id="dBudget" class="form-control" required value="{{ $deliverable->budget }}" />
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm deliverablesRemoveBtnFromServer" data-role="{{ route('my_project.deliverable-destroy', $deliverable->id) }}">&times;</button>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>
                    <input type="text" name="deliverables_name[]" class="form-control deliverablesName" placeholder="Deliverables Name" required>
                </td>
                <td>
                    <input type="number" name="weightage[]" class="form-control deliverablesWeightage" placeholder="Weightage" required>
                </td>
                <td>
                    <input type="date" name="d_start_date[]" class="form-control" required value="{{ date('Y-m-d', time()) }}">
                </td>
                <td>
                    <input type="date" name="d_end_date[]" class="form-control" required value="{{ date('Y-m-d', (time()+(24*60*60))) }}">
                </td>
                <td>
                    <input type="number" name="d_budget[]" id="dBudget" class="form-control" required/>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm deliverablesRemoveBtn">&times;</button>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
    <div class="col-12 text-right">
        <button type="button" class="btn btn-info btn-sm deliverablesAddBtn"><i class="las la-plus">Add</i></button>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary w-75 formSaveBtn">Save</button>
    </div>
</div>