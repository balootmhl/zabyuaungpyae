<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Console\Command;

class IntegrateBranch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branch:integrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New Branch and save branch_id to user related database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $maharshin_branch = Branch::firstOrCreate([
            'name' => 'Maharshin Warehouse',
            'slug' => 'maharshin',
        ]);
        $maharshin = User::findOrFail(1);
        $maharshin->branch_id = $maharshin_branch->id;
        $maharshin->save();
        Product::where('user_id', $maharshin->id)->update(['branch_id' => $maharshin_branch->id]);
        Group::where('user_id', $maharshin->id)->update(['branch_id' => $maharshin_branch->id]);
        Sale::where('user_id', $maharshin->id)->update(['branch_id' => $maharshin_branch->id]);
        Purchase::where('user_id', $maharshin->id)->update(['branch_id' => $maharshin_branch->id]);

        $branch = Branch::firstOrCreate([
            'name' => 'Branch One',
            'slug' => 'branch-one',
        ]);
        $branch_one_user = User::findOrFail(2);
        $branch_one_user->branch_id = $branch->id;
        $branch_one_user->save();
        Product::where('user_id', $branch_one_user->id)->update(['branch_id' => $branch->id]);
        Group::where('user_id', $branch_one_user->id)->update(['branch_id' => $branch->id]);
        Sale::where('user_id', $branch_one_user->id)->update(['branch_id' => $branch->id]);
        Purchase::where('user_id', $branch_one_user->id)->update(['branch_id' => $branch->id]);

        $this->info($branch->name." will take place in place of user from now on.");
    }
}
