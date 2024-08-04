<?php

namespace App\Orchid\Screens;

use App\Models\Group;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class GroupEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Group';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Item Group Name of a warehouse';

    /**
     * @var string
     */
    public $permission = 'platform.module.group';

    /**
     * Query data.
     *
     * @param Group $group
     *
     * @return array
     */
    public function query(Group $group): array
    {
        $this->exists = $group->exists;

        if ($this->exists) {
            $this->name = 'Edit Group';
        }

        return [
            'group' => $group,
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
            Button::make('Create group')
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
                Input::make('group.name')
                    ->title('Group Name')
                    ->required()
                    ->placeholder('Desired Group Label')
                    ->help('Make a label to place items in warehouse.'),

            ]),
        ];
    }

    /**
     * @param Group $group
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Group $group, Request $request)
    {
        $group->fill($request->get('group'))->save();
        $group->user_id = auth()->user()->id;
        $group->save();

        Alert::info('You have updated a group.');

        return redirect()->route('platform.group.list');
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Group $group)
    {
        $group->delete();

        Alert::info('You have successfully deleted the group.');

        return redirect()->route('platform.group.list');
    }
}
