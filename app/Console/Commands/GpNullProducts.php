<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;

class GpNullProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:fix-groupless';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will set dummy group to all products with group_id equal Null.";

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
        // Retrieve all users from the database
        $users = User::all();

        // Display the list of users with numbers
        foreach ($users as $key => $user) {
            $this->line("[$key] {$user->name}");
        }
        // Prompt the user to choose a user by number
        $userNumber = $this->ask('Choose a branch to excecute!');

        // Validate user input
        if (!isset($users[$userNumber])) {
            $this->error('Invalid user.');
            return;
        }
        // Perform required operations with the chosen user
        $chosenUser = $users[$userNumber];

        $group = Group::firstOrCreate(['name' => 'One']);
        $products = Product::where('user_id', $chosenUser->id)->get();
        foreach($products as $product){
            if(is_null($product->group_id) || is_null($product->group)){
                $product->group_id = $group->id;
                $product->save();
            }
        }
        $this->info("{$chosenUser->name}'s groupless products are now in group '{$group->name}'.");
    }
}
