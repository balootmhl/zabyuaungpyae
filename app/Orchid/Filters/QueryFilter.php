<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class QueryFilter extends Filter
{
    /**
     * @var array
     */
    public $parameters = ['key', 'type'];

    /**
     * @return string
     */

    public function name(): string
    {
        return __('Products of');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        if ($this->request->get('type') == 'cat') {
            return $builder->whereHas('category', function (Builder $query) {
                $query->where('name', 'LIKE', '%' . $this->request->get('key') . '%');
            });
        } else {
            return $builder->where('code', 'LIKE', '%' . $this->request->get('key') . '%');
        }

    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Input::make('key')
                ->type('text')
                ->value($this->request->get('key'))
                ->placeholder('Type keyword')
                ->title('Search products'),
            Select::make('type')
                ->options([
                    'code' => 'By Code',
                    'category' => 'By Category',
                ])
                ->value($this->request->get('type'))
                ->title(__('Search Type')),
            // Input::make('submit')->type('submit'),
        ];
    }

    /**
     * @return string
     */
    // public function value(): string
    // {
    //     return $this->name() . ': ' . Product::where('code', $this->request->get('key'))->orwhere('name', $this->request->get('key'))->first()->name;
    // }
}
