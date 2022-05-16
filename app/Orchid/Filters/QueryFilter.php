<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class QueryFilter extends Filter
{
    /**
     * @var array
     */
    public $parameters = ['code'];

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
        return $builder->where('code', 'LIKE', '%' . $this->request->get('code') . '%');
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Input::make('code')
                ->type('text')
                ->value($this->request->get('code'))
                ->placeholder('Search products...')
                ->title('Search'),
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
