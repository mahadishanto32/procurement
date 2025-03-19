<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\FiscalYear;
use \App\Models\PmsModels\Accounts\EntryType;
use \App\Models\PmsModels\Accounts\Tag;
use \App\Models\PmsModels\Accounts\ChartOfAccount;
use \App\Models\PmsModels\Accounts\Entry;
use \App\Models\PmsModels\Accounts\EntryItem;

use App,DB;
use Illuminate\Support\Facades\Auth;

class LedgerStatementController extends Controller
{
    public function index()
    {
        $title = 'Report - Ledger Statement';

        $chart_of_account_id = request()->has('chart_of_account_id') ? request()->get('chart_of_account_id') : 0;
        $from = request()->has('from') ? request()->get('from') : date('Y-m-01');
        $to = request()->has('to') ? request()->get('to') : date('Y-m-t');
        try {
            $data = [
                'title' => $title,
                'chart_of_account_id' => $chart_of_account_id,
                'from' => $from,
                'to' => $to,
                'account' => ChartOfAccount::find($chart_of_account_id),
                'entries' => Entry::whereBetween('date', [$from, $to])
                ->whereHas('items', function($query) use($chart_of_account_id){
                    return $query->where('chart_of_account_id', $chart_of_account_id);
                })
                ->get(),
                'openingBalance' => ledgerClosingBalance($chart_of_account_id, $from, false)['balance'],
                'closingBalance' => ledgerClosingBalance($chart_of_account_id, false, $to)['balance'],
            ];
            
            if(request()->has('pdf')){
                return downloadPDF($data['title'], $data, 'accounting.backend.pages.reports.ledgerStatements.pdf', 'legal', 'portrait');
            }
            return view('accounting.backend.pages.reports.ledgerStatements.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
