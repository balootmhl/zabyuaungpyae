<?php

namespace App\Orchid\Layouts;

use App\Models\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class GroupListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'groups';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('name', 'Group Name')->sort()
                ->render(function (Group $group) {
                    return Link::make($group->name)
                        ->route('platform.group.edit', $group);
                }),

            TD::make('branch_id', 'Branch')->sort()
                ->render(function (Group $group) {
                    if($group->branch){
                        return $group->branch->name;
                    } else {
                        return '';
                    }
                }),

            TD::make('created_at', 'Created')
                ->render(function (Group $group) {
                    return $group->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Last edited')
                ->render(function (Group $group) {
                    return $group->updated_at->toDateTimeString();
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Group $group) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.group.edit', $group->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Are you sure?'))
                                ->method('remove', [
                                    'id' => $group->id,
                                ]),
                        ]);
                }),
        ];
    }
}
