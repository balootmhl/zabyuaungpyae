<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Branch;
use Illuminate\Console\Command;

class ShareProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:duplicate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Choose a branch and share AdminProducts to this branch.';

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
        $users = Branch::all();

        // Display the list of users with numbers
        foreach ($users as $key => $user) {
            if($key != 0){
                $this->line("[$key] {$user->name}");
            }
        }
        // Prompt the user to choose a user by number
        $userNumber = $this->ask('Choose a branch to receive product copies!');

        // Validate user input
        if (!isset($users[$userNumber])) {
            $this->error('Invalid user.');
            return 0;
        }
        // Perform required operations with the chosen user
        $chosenUser = $users[$userNumber];

        $products = Product::where('branch_id', 1)->orderby('created_at', 'asc')->get();
		foreach ($products as $product) {
			$p = new Product();
			$p->code = $product->code;
			$p->name = $product->name;
			$p->user_id = $chosenUser->user_id;
			$p->branch_id = $chosenUser->id;
			$p->category_id = $product->category_id;
			$p->group_id = null;
			$p->buy_price = $product->buy_price;
			$p->sale_price = $product->sale_price;
			$p->quantity = 0;
			$p->save();
		}
        $this->info("{$chosenUser->name} received product copies from Admin with empty stock!");
    }
}
