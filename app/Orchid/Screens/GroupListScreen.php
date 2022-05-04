<?php

namespace App\Orchid\Screens;

use App\Models\Group;
use App\Orchid\Layouts\GroupListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class GroupListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Groups';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All groups fo product';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'groups' => Group::paginate(),
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
            Link::make('Create new')
                ->icon('plus')
                ->route('platform.group.edit'),
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
            Layout::view('products.filter-box'),
            GroupListLayout::class,
        ];
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
