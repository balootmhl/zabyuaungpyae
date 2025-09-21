<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Orchid\Support\Facades\Toast;

class SalesExportController extends Controller
{
    /**
     * Export sales data
     */
    public function export(Request $request)
    {
        try {
            $dateRange = $request->get('date_range');
            $branchId = $request->get('branch_id');
            $exportFormat = $request->get('export_format', 'detailed');
            $user = auth()->user();

            // Validate permissions. You can use Laravel's Gate or Policies for more advanced logic.
            if ($user->id != 1 && $branchId && $branchId != $user->branch->id) {
                // This will not be displayed to the user as a toast on a custom route.
                // You can return a JSON response or redirect back with a session message.
                abort(403, 'You can only export your own branch data.');
            }

            // Use user's branch if not admin
            if ($user->id != 1) {
                $branchId = $user->branch->id;
            }

            // Generate filename
            $dateStr = '';
            if ($dateRange && isset($dateRange['start']) && isset($dateRange['end'])) {
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

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Sales export failed: ' . $e->getMessage());
            // Return an error response
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
