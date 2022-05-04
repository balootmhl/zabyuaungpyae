<?php

namespace App\Orchid\Layouts;

use App\Models\Customer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CustomerListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'customers';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('code', 'Code')->sort(),

            TD::make('name', 'Customer Name')->sort()
                ->render(function (Customer $customer) {
                    return Link::make($customer->name)
                        ->route('platform.customer.edit', $customer);
                }),

            TD::make('phone', 'Phone')->sort(),

            TD::make('address', 'Address')->sort(),

            TD::make('created_at', 'Registered Date')->sort()
                ->render(function (Customer $customer) {
                    return $customer->created_at->toDateString();
                }),

            TD::make('debt', 'Debt')
                ->sort()
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Customer $customer) {
                    if ($customer->debt != null) {
                        return $customer->debt;
                    } else {
                        return '0';
                    }

                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Customer $customer) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.customer.edit', $customer->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Are you sure?'))
                                ->method('remove', [
                                    'id' => $customer->id,
                                ]),
                        ]);
                }),
        ];
    }
}
