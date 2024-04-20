<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class OptimizeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:admin-only';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will erase all products except Super Admin's.";

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
        $products = Product::all();
        foreach($products as $product){
            if($product->user_id != 1){
                $product->delete();
            }
        }
        $this->info('Successfully Deleted!');
    }
}
