<?php

namespace App\Orchid\Screens;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SupplierEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Supplier';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Supplier data for purchase records.';

    /**
     * The permission required to access this screen.

     * @var string
     */
    public $permission = 'platform.people.supplier';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Supplier $supplier): array
    {
        $this->exists = $supplier->exists;

        if ($this->exists) {
            $this->name = 'Edit Supplier';
        }

        return [
            'supplier' => $supplier,
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
            Button::make('Save Customer')
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
                Input::make('supplier.code')->horizontal()
                    ->title('Code')
                    ->required()
                    ->placeholder('Code')
                    ->help('Supplier Code.'),
                Input::make('supplier.name')
                    ->horizontal()
                    ->title('Supplier Name')
                    ->required()
                    ->placeholder('Name')
                    ->help('Full name of supplier.'),
                Input::make('supplier.phone')
                    ->horizontal()
                    ->title('Supplier Phone')
                    ->required()
                    ->placeholder('Phone')
                    ->help('Phone number of supplier.'),
                TextArea::make('supplier.address')
                    ->rows(3)
                    ->horizontal()
                    ->title('Address')
                    ->placeholder('Enter Supplier Address')
                    ->help('Address of supplier.'),
                Input::make('supplier.debt')
                    ->type('number')
                    ->horizontal()
                    ->title('Debt')
                    ->placeholder('Enter Debt')
                    ->help('The debt amount which admin have to pay supplier back.'),
                Button::make(__('Save Supplier'))
                    ->right(true)
                    ->icon('pencil')
                    ->method('createOrUpdate'),
            ]),
        ];
    }

    /**
     * @param Supplier $supplier
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Supplier $supplier, Request $request)
    {
        $supplier->fill($request->get('supplier'))->save();

        Alert::info('You have updated a supplier.');

        return redirect()->route('platform.supplier.list');
    }

    /**
     * @param Supplier $supplier
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Supplier $supplier)
    {
        $supplier->delete();

        Alert::info('You have successfully deleted a supplier.');

        return redirect()->route('platform.supplier.list');
    }
}
