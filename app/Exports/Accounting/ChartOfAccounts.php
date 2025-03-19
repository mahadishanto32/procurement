<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\FromCollection;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
 
class ChartOfAccounts implements FromView
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $fields = $this->data;
 
        return view('accounting.backend.pages.chartOfAccounts.excel', $fields);
    }
}
