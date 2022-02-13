<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomersExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return Customer::all();
    // }
    public function view(): View
    {
        return view('export.customers', [
            'customers' => Customer::all(),
        ]);
    }
}
