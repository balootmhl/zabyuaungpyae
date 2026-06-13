<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;

class IncomeCalculatorScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Income Calculator';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Select date and submit to see calculated total income and total discounts on this day.';

    /**
     * @var string
     */
    public $permission = 'platform.module.calculate-amount';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $request = request();
        $date = $request->get('date', now()->format('Y-m-d'));
        $branchId = $request->get('branch_id');
        $customerId = $request->get('customer_id');

        $query = Sale::query()
            ->whereDate('date', $date);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $invoices = $query->with(['customer', 'branch', 'saleitems.product'])->get();

        // Calculate summary
        $totalInvoices = $invoices->count();
        $totalRevenue = $invoices->sum('grand_total');

        $totalProfit = 0;
        foreach ($invoices as $invoice) {
            $invoiceProfit = 0;
            foreach ($invoice->saleitems as $saleitem) {
                $sellingPrice = $saleitem->price ?? ($saleitem->product->sale_price ?? 0);
                $costPrice = $saleitem->product->buy_price ?? 0;
                $profitPerUnit = $sellingPrice - $costPrice;
                $itemProfit = $profitPerUnit * $saleitem->quantity;
                $invoiceProfit += $itemProfit;
            }
            $invoice->invoice_profit = $invoiceProfit;
            $totalProfit += $invoiceProfit;
        }

        return [
            'date' => $date,
            'branch_id' => $branchId,
            'customer_id' => $customerId,
            'invoices' => $invoices,
            'summary' => [
                'total_invoices' => $totalInvoices,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
            ],
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Group::make([
                    DateTimer::make('date')
                        ->title('Choose Invoice Date')
                        ->required()
                        ->format('Y-m-d'),
                    Select::make('branch_id')
                        ->fromModel(\App\Models\Branch::class, 'name')
                        ->title('Select Branch')
                        ->empty('All Branches')
                        ->placeholder('Choose Branch'),
                    Select::make('customer_id')
                        ->fromModel(\App\Models\Customer::class, 'name')
                        ->title('Select Customer')
                        ->empty('All Customers')
                        ->placeholder('Choose Customer'),
                ])->fullwidth(),
                Button::make(__('Apply Filter'))
                    ->title('')
                    ->icon('filter')
                    ->method('calculate'),
            ]),
            Layout::view('budget.income-display'),

        ];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function calculate(Request $request)
    {
        return redirect()->route('platform.income.discount', [
            'date' => $request->get('date'),
            'branch_id' => $request->get('branch_id'),
            'customer_id' => $request->get('customer_id'),
        ]);
    }
}
