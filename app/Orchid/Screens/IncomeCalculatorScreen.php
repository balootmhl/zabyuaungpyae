<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Screen;
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
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
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
                ])->fullwidth(),
                Button::make(__('Submit'))
                    ->title('')
                    ->icon('pencil')
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
        $invoices = Sale::where('date', $request->get('date'))->where('user_id', auth()->user()->id)->get();
        $total_income = 0;
        $total_discount = 0;
        foreach ($invoices as $invoice) {
            $total_income = $total_income + $invoice->grand_total;
            $total_discount = $total_discount + $invoice->discount;
        }
        session(['total_income' => $total_income, 'total_discount' => $total_discount]);
        // Alert::info('Sale Invoice is deleted successfully. Product Quantity are returning back.');

        return redirect()->route('platform.income.discount')->with(['total_income' => $total_income, 'total_discount' => $total_discount, 'invoices' => $invoices])->withInput();
    }
}
