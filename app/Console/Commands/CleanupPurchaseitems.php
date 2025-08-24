<?php

namespace App\Console\Commands;

use App\Models\Purchaseitem;
use Illuminate\Console\Command;

class CleanupPurchaseitems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:broken-purchaseitems {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up broken purchase items with missing products or purchases';

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

        $this->info('🔍 Analyzing broken purchase items...');
        $this->newLine();

        // Check for missing products
        $missingProducts = Purchaseitem::whereDoesntHave('product')->get();
        $this->warn("Purchase items with missing products: {$missingProducts->count()}");

        // Check for missing purchases
        $missingPurchases = Purchaseitem::whereDoesntHave('purchase')->get();
        $this->warn("Purchase items with missing purchases: {$missingPurchases->count()}");

        // Check for items missing both
        $missingBoth = Purchaseitem::whereDoesntHave('product')
            ->whereDoesntHave('purchase')
            ->get();
        $this->error("Purchase items missing both product AND sale: {$missingBoth->count()}");

        // Show some examples if any broken items exist
        if ($missingProducts->count() > 0 || $missingPurchases->count() > 0) {
            $this->newLine();
            $this->info('📋 Sample broken records:');

            $brokenItems = Purchaseitem::whereDoesntHave('product')
                ->orWhereDoesntHave('purchase')
                ->limit(5)
                ->get(['id', 'product_id', 'purchase_id', 'quantity', 'price']);

            $this->table(
                ['ID', 'Product ID', 'Purchase ID', 'Quantity', 'Price'],
                $brokenItems->toArray()
            );
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info('🏃 DRY RUN MODE - No data will be deleted');
            $totalToDelete = Purchaseitem::whereDoesntHave('product')
                ->orWhereDoesntHave('purchase')
                ->count();
            $this->warn("Would delete {$totalToDelete} broken purchase items");
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
        $deletedMissingProducts = Purchaseitem::whereDoesntHave('product')->delete();
        $this->info("✅ Deleted {$deletedMissingProducts} purchase items with missing products");

        // Delete items with missing purchases (check if any remain after first deletion)
        $deletedMissingPurchases = Purchaseitem::whereDoesntHave('purchase')->delete();
        $this->info("✅ Deleted {$deletedMissingPurchases} purchase items with missing purchases");

        $totalDeleted = $deletedMissingProducts + $deletedMissingPurchases;
        $this->newLine();
        $this->info("🎉 Cleanup complete! Total records deleted: {$totalDeleted}");

        // Final verification
        $remainingBroken = Purchaseitem::whereDoesntHave('product')
            ->orWhereDoesntHave('purchase')
            ->count();

        if ($remainingBroken > 0) {
            $this->warn("⚠️  Warning: {$remainingBroken} broken records still remain");
        } else {
            $this->info("✨ All broken sale items have been cleaned up!");
        }
    }
}
