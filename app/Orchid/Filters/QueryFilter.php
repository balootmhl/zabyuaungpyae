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
    public $parameters = [
        'key',
    ];

    /**
     * @return string
     */
    // public function name(): string
    // {
    //     return __('Products');
    // }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where('key', $this->request->get('key'));
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
