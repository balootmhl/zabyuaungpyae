<?php

namespace App\Orchid\Layouts;

use App\Models\Sale;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SaleListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'sales';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('invoice_no', 'Invoice No.')->sort()
                ->render(function (Sale $sale) {
                    return Link::make($sale->invoice_no)
                        ->route('platform.sale.view', $sale->id);
                }),

            TD::make('custom_name', 'Customer Name')->sort()
                ->render(function (Sale $sale) {
                    return $sale->custom_name;

                }),
            TD::make('user_id', 'Invoice By')->sort()
                ->render(function (Sale $sale) {
                    return $sale->user->name;
                }),
            TD::make('date', 'Issue Date')->sort()
                ->render(function (Sale $sale) {
                    return $sale->date;
                }),

            TD::make('grand_total', 'Grand Total')
                ->render(function (Sale $sale) {
                    return $sale->grand_total . ' MMK';
                })->sort(),

            TD::make('items', 'Items')
                ->render(function (Sale $sale) {
                    return count($sale->saleitems);
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Sale $sale) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.sale.edit', $sale->id)
                                ->icon('pencil'),

                            Link::make(__('Edit beta'))
                                ->route('platform.sale.edit-custom', $sale->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Are you sure?'))
                                ->method('remove', [
                                    'id' => $sale->id,
                                ]),
                        ]);
                }),
        ];
    }
}
