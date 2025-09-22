<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Sale;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SalesExportController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get branches for dropdown
        $branches = Branch::all();

        // Get sales count for preview
        $salesQuery = Sale::query();
        if ($user->id != 1) {
            $salesQuery->where('branch_id', $user->branch->id);
        }

        $totalSales = $salesQuery->count();
        $thisMonthSales = $salesQuery->whereMonth('created_at', now()->month)->count();

        return view('sales-export.index', compact('branches', 'totalSales', 'thisMonthSales', 'user'));
    }

    public function export(Request $request)
    {
        $dateRange = [
            'start' => $request->get('date_start'),
            'end' => $request->get('date_end')
        ];
        $branchId = $request->get('branch_id');
        $exportFormat = $request->get('export_format', 'detailed');
        $user = auth()->user();

        // Validate permissions
        if ($user->id != 1 && $branchId && $branchId != $user->branch->id) {
            return back()->with('error', 'You can only export your own branch data.');
        }

        // Use user's branch if not admin
        if ($user->id != 1) {
            $branchId = $user->branch->id;
        }

        // Generate filename
        $dateStr = '';
        if ($dateRange['start'] && $dateRange['end']) {
            $dateStr = '_' . Carbon::parse($dateRange['start'])->format('Y-m-d') .
                      '_to_' . Carbon::parse($dateRange['end'])->format('Y-m-d');
        }

        $branchName = 'all';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $branchName = $branch ? str_replace(' ', '_', strtolower($branch->name)) : 'unknown';
        }

        $filename = 'sales_export_' . $branchName . $dateStr . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Create export with parameters
        return Excel::download(new SalesExport($branchId, $dateRange, $exportFormat), $filename);
    }

    public function preview(Request $request)
    {
        $dateRange = [
            'start' => $request->get('date_start'),
            'end' => $request->get('date_end')
        ];
        $branchId = $request->get('branch_id');
        $user = auth()->user();

        // Build query
        $query = Sale::with(['customer', 'branch']);

        // Apply date filter
        if ($dateRange['start'] && $dateRange['end']) {
            $startDate = Carbon::parse($dateRange['start'])->startOfDay();
            $endDate = Carbon::parse($dateRange['end'])->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply branch filter
        if ($user->id == 1 && $branchId) {
            $query->where('branch_id', $branchId);
        } elseif ($user->id != 1) {
            $query->where('branch_id', $user->branch->id);
        }

        $count = $query->count();
        $totalAmount = $query->sum('grand_total');

        return back()->with('success', "Preview: {$count} sales records found. Total amount: " . number_format($totalAmount, 2));
    }
}
