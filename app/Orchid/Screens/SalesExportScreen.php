<?php

namespace App\Orchid\Screens;

use App\Models\Branch;
use App\Models\Sale;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExportScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Export Sales Data';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Export sales invoices with date range and branch filters.';

    /**
     * The permission required to access this screen.
     *
     * @var string
     */
    public $permission = 'platform.module.sale';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $user = auth()->user();

        // Get sales count for preview
        $salesQuery = Sale::query();

        if ($user->id != 1) {
            // Regular users see only their branch
            $salesQuery->where('branch_id', $user->branch->id);
        }

        $totalSales = $salesQuery->count();
        $thisMonthSales = $salesQuery->whereMonth('created_at', now()->month)->count();

        return [
            'total_sales' => $totalSales,
            'this_month_sales' => $thisMonthSales,
            'user_branch' => $user->branch->name ?? 'All Branches',
            'is_admin' => $user->id == 1,
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Preview Data')
                ->icon('eye')
                ->method('previewData')
                ->parameters(['preview' => true]),

            // Button::make('Export Excel')
            //     ->icon('cloud-download')
            //     ->method('exportSales'),

            Button::make('Export Excel')
                ->icon('cloud-download')
                ->method('exportSalesRoute'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        $user = auth()->user();

        $layouts = [
            Layout::rows([
                DateRange::make('date_range')
                    ->title('Select Date Range')
                    ->help('Choose the date range for sales export. Leave empty to export all dates.')
                    ->placeholder('Select dates'),

                Select::make('branch_id')
                    ->fromModel(Branch::class, 'name')
                    ->title('Select Branch')
                    ->placeholder('Choose Branch')
                    ->empty('All Branches', '')
                    ->help($user->id == 1 ? 'Admin can export any branch or all branches' : 'You can only export your branch data')
                    ->canSee($user->id == 1),

                Select::make('export_format')
                    ->options([
                        'detailed' => 'Detailed Export (All columns)',
                        'summary' => 'Summary Export (Key data only)',
                        'items' => 'Items Export (Product details)',
                    ])
                    ->title('Export Format')
                    ->value('detailed')
                    ->help('Choose what data to include in the export'),
            ]),
        ];

        // Add preview section
        $layouts[] = Layout::view('orchid.sales-export-info', [
            'total_sales' => $this->query()['total_sales'],
            'this_month_sales' => $this->query()['this_month_sales'],
            'user_branch' => $this->query()['user_branch'],
            'is_admin' => $this->query()['is_admin'],
        ]);

        // Add custom JavaScript for new tab functionality
        $layouts[] = Layout::view('orchid.sales-export-script');

        return $layouts;
    }

    /**
     * Preview data before export
     */
    public function previewData(Request $request)
    {
        try {
            $dateRange = $request->get('date_range');
            $branchId = $request->get('branch_id');
            $user = auth()->user();

            // Build query
            $query = Sale::with(['customer', 'branch']);

            // Apply date filter
            if ($dateRange && isset($dateRange['start']) && isset($dateRange['end'])) {
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

            Toast::info("Preview: {$count} sales records found. Total amount: " . number_format($totalAmount, 2));

            return redirect()->route('platform.sales.export');

        } catch (\Exception $e) {
            Toast::error('Preview failed: ' . $e->getMessage());
            return redirect()->route('platform.sales.export');
        }
    }

    /**
     * Export sales data
     */
    public function exportSales(Request $request)
    {
        try {
            $dateRange = $request->get('date_range');
            $branchId = $request->get('branch_id');
            $exportFormat = $request->get('export_format', 'detailed');
            $user = auth()->user();

            // Validate permissions
            if ($user->id != 1 && $branchId && $branchId != $user->branch->id) {
                Toast::error('You can only export your own branch data.');
                return redirect()->route('platform.sales.export');
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
            Toast::error('Export failed: ' . $e->getMessage());
            return redirect()->route('platform.sales.export');
        }
    }

    // Add a new method to handle the redirect to the custom route
    public function exportSalesRoute(Request $request)
    {
        // Collect the form data
        $dateRange = $request->get('date_range');
        $branchId = $request->get('branch_id');
        $exportFormat = $request->get('export_format');

        // Build the URL with the query parameters
        $url = route('platform.sales.export.file', [
            'date_range[start]' => $dateRange['start'] ?? null,
            'date_range[end]' => $dateRange['end'] ?? null,
            'branch_id' => $branchId,
            'export_format' => $exportFormat,
        ]);

        // Redirect the user to the custom route
        return redirect()->route('platform.sales.export.file', [
            'date_range[start]' => $dateRange['start'] ?? null,
            'date_range[end]' => $dateRange['end'] ?? null,
            'branch_id' => $branchId,
            'export_format' => $exportFormat,
        ]);

    }
}
