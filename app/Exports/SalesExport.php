<?php
namespace App\Exports;

use App\Models\Sale;
use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
    protected $branch_id;

    function __construct($branch_id = null) {
        $this->branch_id = $branch_id;
    }

    public function view(): View
    {
        $query = Sale::with(['saleitems.product', 'customer', 'user', 'branch']);

        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        } else {
            // Default to current user's branch if no branch specified
            $query->where('branch_id', auth()->user()->branch->id);
        }

        $branch_name = 'All Branches';
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $branch_name = $branch ? $branch->name : 'Unknown Branch';
        }

        return view('export.sales', [
            'sales' => $query->orderBy('created_at', 'desc')->get(),
            'branch_name' => $branch_name,
        ]);
    }
}
