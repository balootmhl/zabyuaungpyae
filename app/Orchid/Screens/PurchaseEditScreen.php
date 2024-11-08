<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PurchaseEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Purchase Invoice';

    /**
     * The permission required to access this screen.

     * @var string
     */
    public $permission = 'platform.module.purchase';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Purchase $purchase): array
    {
        $this->exists = $purchase->exists;

        if ($this->exists) {
            $this->name = 'Edit Purchase Invoice';
        }

        return [
            'purchase' => $purchase,
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
            Button::make('Save')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Delete')
                ->icon('trash')
                ->confirm(__('Are you sure?'))
                ->method('remove')
                ->canSee($this->exists),
        ];
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
                    Relation::make('purchase.user_id')
                        ->fromModel(User::class, 'name')
                        ->title('Select Admin or Branch')
                        ->required()
                        ->help('Admin or Branch who is in charge.'),
                    DateTimer::make('purchase.date')
                        ->title('Date')
                        ->format('Y-m-d')
                        ->help("Invoice issued Date."),
                    Relation::make('purchase.supplier_id')
                        ->fromModel(Supplier::class, 'name')
                        ->title('Choose Supplier')
                        ->required()
                        ->help('Choose customer for purchase invoice.'),
                ])->fullWidth(),
                Group::make([
                    Matrix::make('items')
                        ->title('Add Invoice Items')
                        ->required()
                        ->columns([
                            'Product Code' => 'product_id', 'Quantity' => 'qty',
                        ])
                        ->fields([
                            'product_id' => Select::make('product_id')
                                ->fromModel(Product::class, 'code')
                                ->required()
                                ->empty('No select')
                                ->placeholder('Choose Product'),
                            'qty' => Input::make('quantity')
                                ->type('number')
                                ->required()
                                ->placeholder('Enter Quantity'),
                        ]),
                ])->fullWidth(),
                Group::make([
                    Input::make('purchase.invoice_no')
                        ->title("Invoice No.")
                        ->disabled()
                        ->help("Will be auto generated by system."),
                    // Select::make('purchase.is_cash')
                    //     ->options([
                    //         '1' => 'Yes',
                    //         '0' => 'No',
                    //     ])
                    //     ->title('Is Cash Down Payment')
                    //     ->help('Select whether it is cash down or not.'),
                    Input::make('purchase.discount')
                        ->type('number')
                        ->value(0)
                        ->title("Discount Amount")
                        ->placeholder('Enter Discount')
                        ->help("To give discount for the customer."),
                    // Select::make('purchase.status')
                    //     ->options([
                    //         'paid' => 'Paid',
                    //         'pending' => 'Not Paid Yet',
                    //     ])
                    //     ->title('Status')
                    //     ->help('Select whether it is cash down or not.'),
                    Input::make('purchase.received')
                        ->type('number')
                        ->value(0)
                        ->title("Received Amount")
                        ->placeholder('Enter Received')
                        ->help("To save the amount customer paid for this invoice."),
                ])->fullWidth(),
                // Group::make([
                //     TextArea::make('purchase.remarks')
                //         ->rows(2)
                //         ->title("Remarks")
                //         ->placeholder('Write remarks')
                //         ->help("To note something bold about this invoice."),
                // ])->fullWidth(),

            ]),
            Layout::view('purchases.purchaseitems-table'),
            Layout::rows([
                Button::make(__('Save Invoice'))
                    ->icon('pencil')
                    ->method('createOrUpdate'),
            ]),

        ];
    }

    /**
     * @param Purchase $purchase
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Purchase $purchase, Request $request)
    {
        // dd($request->all());
        $purchase->fill($request->get('purchase'))->save();
        if ($purchase->user_id == null) {
            $purchase->user_id = $request->get('purchase')['user_id'];
        }

        if ($request->has('items')) {
            $items = $request->get('items');
            foreach ($items as $item) {
                $purchaseitem = new Purchaseitem();
                $purchaseitem->product_id = $item['product_id'];
                $purchaseitem->purchase_id = $purchase->id;
                $purchaseitem->quantity = $item['qty'];
                $purchaseitem->save();
                $product = Product::findOrFail($purchaseitem->product_id);
                $product->quantity = $product->quantity + $purchaseitem->quantity;
                $product->update();
            }
        }

        if ($request->has('olditems')) {
            $olds = $request->get('olditems');
            foreach ($olds as $old) {
                $olditem = Purchaseitem::findOrFail($old['id']);
                if ($olditem->quantity != $old['qty']) {
                    $p = Product::findOrFail($olditem->product_id);
                    $p->quantity = $p->quantity - $olditem->quantity;
                    $p->update();
                    $olditem->quantity = $old['qty'];
                    $olditem->update();
                    $p->quantity = $p->quantity + $old['qty'];
                    $p->update();
                }

            }
        }

        $subtotal = 0;

        foreach ($purchase->purchaseitems as $pitem) {
            $item_total = $pitem->product->buy_price * $pitem->quantity;
            $subtotal = $subtotal + $item_total;
        }

        $purchase->sub_total = $subtotal;
        $purchase->grand_total = $subtotal - $purchase->discount;
        if ($purchase->invoice_no == null) {
            $purchase->invoice_no = '#02' . str_replace("-", "", $purchase->date) . $purchase->id;
        }
        $purchase->update();

        Alert::info('You have updated a purchase invoice.');

        return redirect()->route('platform.purchase.view', $purchase->id);
    }

    /**
     * @param Purchase $purchase
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Purchase $purchase)
    {
        $purchaseitems = $purchase->purchaseitems;
        if ($purchaseitems) {
            foreach ($purchaseitems as $purchaseitem) {
                $product = Product::findOrFail($purchaseitem->product_id);
                $product->quantity = $product->quantity - $purchaseitem->quantity;
                $product->update();
                $purchaseitem->delete();
            }
        }
        $purchase->delete();

        Alert::info('Purchase Invoice is deleted successfully. Product Quantity are returning back.');

        return redirect()->route('platform.purchase.list');
    }
}
