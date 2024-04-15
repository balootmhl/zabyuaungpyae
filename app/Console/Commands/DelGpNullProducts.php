<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class DelGpNullProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:gp-null-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will erase all products with group_id equal Null.";

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
            if(is_null($product->group_id)){
                $product->delete();
            }
        }
        $this->info('Successfully Deleted!');
    }
}
