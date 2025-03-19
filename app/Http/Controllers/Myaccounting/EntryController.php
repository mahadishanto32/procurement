<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Company;
use \App\Models\PmsModels\Accounts\CostCentre;
use \App\Models\PmsModels\Accounts\FiscalYear;
use \App\Models\PmsModels\Accounts\EntryType;
use \App\Models\PmsModels\Accounts\Tag;
use \App\Models\PmsModels\Accounts\ChartOfAccount;
use \App\Models\PmsModels\Accounts\Entry;
use \App\Models\PmsModels\Accounts\EntryItem;

use App,DB;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller
{
    public function index()
    {
        $title = 'Entries';

        $from = request()->has('from') ? request()->get('from') : date('Y-m-01');
        $to = request()->has('to') ? request()->get('to') : date('Y-m-t');
        $entry_type_id = request()->has('entry_type_id') ? request()->get('entry_type_id') : 0;
        $tag_id = request()->has('tag_id') ? request()->get('tag_id') : 0;
        $chart_of_account_id = request()->has('chart_of_account_id') ? request()->get('chart_of_account_id') : 0;

        try {
            $data = [
                'title' => $title,
                'from' => $from,
                'to' => $to,
                'entry_type_id' => $entry_type_id,
                'tag_id' => $tag_id,
                'chart_of_account_id' => $chart_of_account_id,
                'entryTypes' => EntryType::all(),
                'tags' => Tag::all(),
                'entries' => Entry::whereBetween('date', [$from, $to])
                ->when($entry_type_id > 0, function($query) use($entry_type_id){
                    return $query->where('entry_type_id', $entry_type_id);
                })
                ->when($tag_id > 0, function($query) use($tag_id){
                    return $query->where('tag_id', $tag_id);
                })
                ->when($chart_of_account_id > 0, function($query) use($chart_of_account_id){
                    return $query->whereHas('items', function($query) use($chart_of_account_id){
                        return $query->where('chart_of_account_id', $chart_of_account_id);
                    });
                })
                ->get(),
            ];

            if(request()->has('pdf')){
                return downloadPDF($title, $data, 'accounting.backend.pages.entries.pdf', 'legal', 'landscape');
            }

            return view('accounting.backend.pages.entries.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $entryType = EntryType::where('label', request()->get('type'))->first();
        if(!isset($entryType->id)){
            return redirect('accounting/entries');
        }

        $companies = Company::has('costCentres')->get();
        $costCentres = '<option selected value="0" data-company-id="">Choose Cost Centre</option>';
        if(isset($companies[0])){
            foreach($companies as $key => $company){
                $costCentres .= '<optgroup label="['.$company->code.'] '.$company->name.'">';
                foreach($company->costCentres as $key => $costCentre){
                    $costCentres .= '<option value="'.$costCentre->id.'"  data-company-id="'.$costCentre->company_id.'">&nbsp;&nbsp;['.$costCentre->code.'] '.$costCentre->name.'</option>';
                }
                $costCentres .= '</optgroup>';
            }
        }

        $data = [
            'title' => 'New '.$entryType->name.' Entry',
            'entryType' => $entryType,
            'tags' => Tag::all(),
            'fiscalYear' => FiscalYear::where('closed', 0)->first(),
            'costCentres' => $costCentres,
            'chartOfAccountsOptions' => chartOfAccountsOptions([], 0),
            'code' => uniqueCode(strlen($entryType->prefix)+12,$entryType->prefix.'-'.date('ymd').'-','entries','code'),
        ];

        return view('accounting.backend.pages.entries.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:entries',
            'number' => 'required|unique:entries',
            'cost_centre_id' => 'required',
            'cost_centre_id.*' => 'required',
            'chart_of_account_id' => 'required',
            'chart_of_account_id.*' => 'required',
        ]);

        if($this->amount($request)['debit'] <= 0 || $this->amount($request)['credit'] <= 0){
            return response()->json([
                'success' => false,
                'message' => "Please add some Debit or Credit amount"
            ]);
        }

        if($this->amount($request)['debit'] != $this->amount($request)['credit']){
            return response()->json([
                'success' => false,
                'message' => "Debit & Credit amount must be same"
            ]);
        }

        DB::beginTransaction();
        try{
            $entryType = EntryType::where('label', request()->get('type'))->first();
            $entry = Entry::create([
                'code' => uniqueCode(strlen($entryType->prefix)+12,$entryType->prefix.'-'.date('ymd').'-','entries','code'),
                'fiscal_year_id' => $request->fiscal_year_id,
                'entry_type_id' => $entryType->id,
                'tag_id' => $request->tag_id,
                'number' => $request->number,
                'date' => $request->date,
                'debit' => $this->amount($request)['debit'],
                'credit' => $this->amount($request)['credit'],
                'notes' => $request->notes,
            ]);

            if(isset($request->chart_of_account_id[0])){
                foreach($request->chart_of_account_id as $key => $chart_of_account_id){
                    $account = ChartOfAccount::find($chart_of_account_id);
                    EntryItem::create([
                        'entry_id' => $entry->id,
                        'cost_centre_id' => $request->cost_centre_id[$key],
                        'chart_of_account_id' => $request->chart_of_account_id[$key],
                        'amount' => $request->amount[$key],
                        'debit_credit' => $account->type,
                        'narration' => $request->narration[$key],
                    ]);
                }
            }
            
            DB::commit();
            session()->flash('alert-type', 'success');
            session()->flash('message', 'Entry has been saved successfully');
            return response()->json([
                'success' => true,
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $entry = Entry::findOrFail($id);
        $data = [
            'title' => $entry->entryType->name.' Entry #'.$entry->code,
            'entry' => $entry,
        ];

        if(request()->has('pdf')){
            return downloadPDF($data['title'], $data, 'accounting.backend.pages.entries.entry-pdf', 'legal', 'landscape');
        }

        return view('accounting.backend.pages.entries.entry', $data);
    }

    public function edit($id)
    {
        $entry = Entry::findOrFail($id);
        $companies = Company::has('costCentres')->get();
        $costCentres = '<option selected value="0" data-company-id="">Choose Cost Centre</option>';
        if(isset($companies[0])){
            foreach($companies as $key => $company){
                $costCentres .= '<optgroup label="['.$company->code.'] '.$company->name.'">';
                foreach($company->costCentres as $key => $costCentre){
                    $costCentres .= '<option value="'.$costCentre->id.'"  data-company-id="'.$costCentre->company_id.'">&nbsp;&nbsp;['.$costCentre->code.'] '.$costCentre->name.'</option>';
                }
                $costCentres .= '</optgroup>';
            }
        }
        $data = [
            'title' => 'Edit '.$entry->entryType->name.' Entry #'.$entry->code,
            'tags' => Tag::all(),
            'fiscalYear' => FiscalYear::where('closed', 0)->first(),
            'chartOfAccountsOptions' => chartOfAccountsOptions([], 0),
            'entry' => $entry,
            'companies' => $companies,
            'costCentres' => $costCentres
        ];

        return view('accounting.backend.pages.entries.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:entries,code,'.$id,
            'number' => 'required|unique:entries,number,'.$id,
            'cost_centre_id' => 'required',
            'cost_centre_id.*' => 'required',
            'chart_of_account_id' => 'required',
            'chart_of_account_id.*' => 'required',
        ]);

        if($this->amount($request)['debit'] <= 0 || $this->amount($request)['credit'] <= 0){
            return response()->json([
                'success' => false,
                'message' => "Please add some Debit or Credit amount"
            ]);
        }

        if($this->amount($request)['debit'] != $this->amount($request)['credit']){
            return response()->json([
                'success' => false,
                'message' => "Debit & Credit amount must be same"
            ]);
        }

        DB::beginTransaction();
        try{
            $entry = Entry::find($id)->update([
                'tag_id' => $request->tag_id,
                'number' => $request->number,
                'date' => $request->date,
                'debit' => $this->amount($request)['debit'],
                'credit' => $this->amount($request)['credit'],
                'notes' => $request->notes,
            ]);

            if(isset($request->chart_of_account_id[0])){
                EntryItem::where('entry_id', $id)->delete();
                foreach($request->chart_of_account_id as $key => $chart_of_account_id){
                    $account = ChartOfAccount::find($chart_of_account_id);
                    EntryItem::create([
                        'entry_id' => $entry->id,
                        'cost_centre_id' => $request->cost_centre_id[$key],
                        'chart_of_account_id' => $request->chart_of_account_id[$key],
                        'amount' => $request->amount[$key],
                        'debit_credit' => $account->type,
                        'narration' => $request->narration[$key],
                    ]);
                }
            }
            
            DB::commit();
            session()->flash('alert-type', 'success');
            session()->flash('message', 'Entry has been updated successfully');
            return response()->json([
                'success' => true,
            ]);
        }catch (\Throwable $th){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try{
            Entry::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Entry has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function amount($request)
    {
        $debit = 0;
        $credit = 0;

        if(isset($request->chart_of_account_id[0])){
            foreach($request->chart_of_account_id as $key => $value){
                $account = ChartOfAccount::find($value);
                $debit += ($account->type == "D" ? $request->amount[$key] : 0);
                $credit += ($account->type == "C" ? $request->amount[$key] : 0);
            }
        }

        return [
            'debit' => $debit,
            'credit' => $credit,
        ];
    }
}
