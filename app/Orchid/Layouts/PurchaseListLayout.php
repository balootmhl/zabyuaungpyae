<?php

namespace App\Orchid\Layouts;

use App\Models\Purchase;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PurchaseListLayout extends Table {
	/**
	 * Data source.
	 *
	 * The name of the key to fetch it from the query.
	 * The results of which will be elements of the table.
	 *
	 * @var string
	 */
	protected $target = 'purchases';

	/**
	 * Get the table cells to be displayed.
	 *
	 * @return TD[]
	 */
	protected function columns(): array
	{
		return [
			TD::make('invoice_no', 'Invoice No.')->sort()
				->render(function (Purchase $purchase) {
					return Link::make($purchase->invoice_no)
						->route('platform.purchase.view', $purchase->id);
				}),

			TD::make('customer_name', 'Supplier Name')->sort()
				->render(function (Purchase $purchase) {
					return $purchase->supplier->name;
				}),

            TD::make('branch_id', 'Branch')->sort()
                ->render(function (Purchase $purchase) {
                    if($purchase->branch){
                        return $purchase->branch->name;
                    } else {
                        return '';
                    }
                }),

            TD::make('items', 'Items')
                ->render(function (Purchase $purchase) {
                    $items = $purchase->purchaseitems;
                    $array = [];
                    foreach($items as $item){
                        $array[] = '['.$item->product_id.']['.$item->code.']['.$item->name.'](Qty-'.$item->quantity.')';
                    }
                    $value = implode(', <br>', $array);
                    return $value;
                }),

			TD::make('user_id', 'Invoice By')->sort()
				->render(function (Purchase $purchase) {
					return $purchase->user->name;
				}),
			TD::make('date', 'Issue Date')->sort()
				->render(function (Purchase $purchase) {
					return $purchase->date;
				}),

			TD::make('grand_total', 'Grand Total')
				->render(function (Purchase $purchase) {
					return $purchase->grand_total . ' MMK';
				})->sort(),

			TD::make(__('Actions'))
				->align(TD::ALIGN_CENTER)
				->width('100px')
				->render(function (Purchase $purchase) {
					return DropDown::make()
						->icon('options-vertical')
						->list([

							// Link::make(__('Edit'))
							//     ->route('platform.purchase.edit', $purchase->id)
							//     ->icon('pencil'),

							Link::make(__('Edit'))
								->route('platform.purchase.edit-custom', $purchase->id)
								->icon('pencil'),

							Button::make(__('Delete'))
								->icon('trash')
								->confirm(__('Are you sure?'))
								->method('remove', [
									'id' => $purchase->id,
								]),
						]);
				}),
		];
	}
}
