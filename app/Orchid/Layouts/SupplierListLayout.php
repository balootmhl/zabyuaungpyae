<?php

namespace App\Orchid\Layouts;

use App\Models\Supplier;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SupplierListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'suppliers';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('code', 'Code')->sort(),

            TD::make('name', 'Supplier Name')->sort()
                ->render(function (Supplier $supplier) {
                    return Link::make($supplier->name)
                        ->route('platform.supplier.edit', $supplier);
                }),

            TD::make('phone', 'Phone')->sort(),

            TD::make('address', 'Address')->sort(),

            TD::make('created_at', 'Registered Date')->sort()
                ->render(function (Supplier $supplier) {
                    return $supplier->created_at->toDateString();
                }),

            TD::make('debt', 'Debt')
                ->sort()
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Supplier $supplier) {
                    if ($supplier->debt != null) {
                        return $supplier->debt;
                    } else {
                        return '0';
                    }

                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Supplier $supplier) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.supplier.edit', $supplier->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Are you sure?'))
                                ->method('remove', [
                                    'id' => $supplier->id,
                                ]),
                        ]);
                }),
        ];
    }
}
