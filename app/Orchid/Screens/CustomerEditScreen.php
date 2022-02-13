<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CustomerEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Customer';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Customer data for sale records.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Customer $customer): array
    {
        $this->exists = $customer->exists;

        if ($this->exists) {
            $this->name = 'Edit Customer';
        }

        return [
            'customer' => $customer,
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
                Input::make('customer.code')->horizontal()
                    ->title('Code')
                    ->required()
                    ->placeholder('Code')
                    ->help('Code of customer.'),
                Input::make('customer.name')
                    ->horizontal()
                    ->title('Customer Name')
                    ->required()
                    ->placeholder('Name')
                    ->help('Full name of customer.'),
                Input::make('customer.phone')
                    ->horizontal()
                    ->title('Customer Phone')
                    ->required()
                    ->placeholder('Phone')
                    ->help('Phone number of customer.'),
                TextArea::make('customer.address')
                    ->rows(3)
                    ->horizontal()
                    ->title('Address')
                    ->placeholder('Enter Customer Address')
                    ->help('Address of customer.'),
                Input::make('customer.debt')
                    ->type('number')
                    ->horizontal()
                    ->title('Debt')
                    ->placeholder('Enter Debt')
                    ->help('The debt amount which customer have to pay back.'),
                Button::make(__('Save Customer'))
                    ->right(true)
                    ->icon('pencil')
                    ->method('createOrUpdate'),
            ]),
        ];
    }

    /**
     * @param Customer $customer
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Customer $customer, Request $request)
    {
        $customer->fill($request->get('customer'))->save();

        Alert::info('You have updated a customer.');

        return redirect()->route('platform.customer.list');
    }

    /**
     * @param Customer $customer
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Customer $customer)
    {
        $customer->delete();

        Alert::info('You have successfully deleted a customer.');

        return redirect()->route('platform.customer.list');
    }
}
