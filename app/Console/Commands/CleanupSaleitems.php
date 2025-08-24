<?php

namespace App\Console\Commands;

use App\Models\Saleitem;
use Illuminate\Console\Command;

class CleanupSaleitems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:broken-saleitems {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up broken sale items with missing products or sales';

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
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Analyzing broken sale items...');
        $this->newLine();

        // Check for missing products
        $missingProducts = Saleitem::whereDoesntHave('product')->get();
        $this->warn("Sale items with missing products: {$missingProducts->count()}");

        // Check for missing sales
        $missingSales = Saleitem::whereDoesntHave('sale')->get();
        $this->warn("Sale items with missing sales: {$missingSales->count()}");

        // Check for items missing both
        $missingBoth = Saleitem::whereDoesntHave('product')
            ->whereDoesntHave('sale')
            ->get();
        $this->error("Sale items missing both product AND sale: {$missingBoth->count()}");

        // Show some examples if any broken items exist
        if ($missingProducts->count() > 0 || $missingSales->count() > 0) {
            $this->newLine();
            $this->info('📋 Sample broken records:');

            $brokenItems = Saleitem::whereDoesntHave('product')
                ->orWhereDoesntHave('sale')
                ->limit(5)
                ->get(['id', 'product_id', 'sale_id', 'quantity', 'price']);

            $this->table(
                ['ID', 'Product ID', 'Sale ID', 'Quantity', 'Price'],
                $brokenItems->toArray()
            );
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info('🏃 DRY RUN MODE - No data will be deleted');
            $totalToDelete = Saleitem::whereDoesntHave('product')
                ->orWhereDoesntHave('sale')
                ->count();
            $this->warn("Would delete {$totalToDelete} broken sale items");
            return;
        }

        // Ask for confirmation
        if (!$this->confirm('Do you want to proceed with cleanup? This will permanently delete broken records.')) {
            $this->info('Cleanup cancelled.');
            return;
        }

        // Perform cleanup
        $this->info('🧹 Starting cleanup...');

        // Delete items with missing products
        $deletedMissingProducts = Saleitem::whereDoesntHave('product')->delete();
        $this->info("✅ Deleted {$deletedMissingProducts} sale items with missing products");

        // Delete items with missing sales (check if any remain after first deletion)
        $deletedMissingSales = Saleitem::whereDoesntHave('sale')->delete();
        $this->info("✅ Deleted {$deletedMissingSales} sale items with missing sales");

        $totalDeleted = $deletedMissingProducts + $deletedMissingSales;
        $this->newLine();
        $this->info("🎉 Cleanup complete! Total records deleted: {$totalDeleted}");

        // Final verification
        $remainingBroken = Saleitem::whereDoesntHave('product')
            ->orWhereDoesntHave('sale')
            ->count();

        if ($remainingBroken > 0) {
            $this->warn("⚠️  Warning: {$remainingBroken} broken records still remain");
        } else {
            $this->info("✨ All broken sale items have been cleaned up!");
        }
    }
}
