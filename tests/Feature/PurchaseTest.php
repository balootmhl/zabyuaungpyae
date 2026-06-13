<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomController;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $branch;
    protected $supplier;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable CSRF for testing
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Yangon Branch',
            'slug' => 'yangon-branch',
            'phone' => '091234567',
            'address' => 'Yangon',
        ]);

        // Create user
        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'permissions' => [
                'platform.index' => true,
                'platform.module.purchase' => true,
            ],
        ]);

        // Create supplier
        $this->supplier = Supplier::create([
            'name' => 'Supplier A',
            'phone' => '099876543',
            'address' => 'Mandalay',
        ]);

        // Create category
        $category = new Category();
        $category->code = 'C-001';
        $category->name = 'Test Category';
        $category->save();

        // Create product
        $this->product = new Product();
        $this->product->code = 'P-001';
        $this->product->name = 'Test Product';
        $this->product->category_id = $category->id;
        $this->product->buy_price = 5000;
        $this->product->sale_price = 7000;
        $this->product->quantity = 100;
        $this->product->branch_id = $this->branch->id;
        $this->product->save();
    }

    /**
     * Test creating a purchase invoice successfully.
     */
    public function test_store_purchase_successfully()
    {
        $response = $this->actingAs($this->user)
            ->post(route('platform.purchase.store-custom'), [
                'supplier_id' => 'Supplier A', // firstOrCreate by name
                'invoice_code' => '001',
                'is_inv_auto' => 0,
                'date' => '2026-06-13',
                'address' => 'Mandalay',
                'discount' => 1000,
                'received' => 5000,
                'product' => $this->product->id,
                'price' => 5000,
                'qty' => 10,
            ]);

        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $response->assertRedirect(route('platform.purchase.edit-custom', $purchase->id));

        // Assert purchase details
        $this->assertEquals('#' . date('y') . date('m') . '001', $purchase->invoice_no);
        $this->assertEquals(50000, $purchase->sub_total); // 5000 * 10
        $this->assertEquals(49000, $purchase->grand_total); // 50000 - 1000
        $this->assertEquals(44000, $purchase->remained); // 49000 - 5000

        // Assert product quantity incremented
        $this->product->refresh();
        $this->assertEquals(110, $this->product->quantity);
    }

    /**
     * Test updating a purchase invoice.
     */
    public function test_update_purchase_successfully()
    {
        $purchase = new Purchase();
        $purchase->invoice_no = 'PUR-001';
        $purchase->user_id = $this->user->id;
        $purchase->branch_id = $this->branch->id;
        $purchase->supplier_id = $this->supplier->id;
        $purchase->date = '2026-06-13';
        $purchase->custom_name = $this->supplier->name;
        $purchase->discount = 1000;
        $purchase->sub_total = 50000;
        $purchase->grand_total = 49000;
        $purchase->received = 5000;
        $purchase->remained = 44000;
        $purchase->save();

        $purchaseitem = new Purchaseitem();
        $purchaseitem->product_id = $this->product->id;
        $purchaseitem->purchase_id = $purchase->id;
        $purchaseitem->code = $this->product->code;
        $purchaseitem->name = $this->product->name;
        $purchaseitem->quantity = 10;
        $purchaseitem->price = 5000;
        $purchaseitem->save();

        $response = $this->actingAs($this->user)
            ->post(route('platform.purchase.update-custom'), [
                'purchase_id' => $purchase->id,
                'supplier_id' => 'Supplier A',
                'invoice_code' => '001',
                'is_inv_auto' => 0,
                'date' => '2026-06-13',
                'address' => 'Mandalay',
                'discount' => 2000,
                'product' => $this->product->id,
                'price' => 5000,
                'qty' => 5, // Add 5 more qty
            ]);

        $response->assertRedirect(route('platform.purchase.edit-custom', $purchase->id));

        $purchase->refresh();
        $this->assertEquals(75000, $purchase->sub_total); // (10+5) * 5000
        $this->assertEquals(73000, $purchase->grand_total); // 75000 - 2000

        // Assert product stock updated (100 + 5 = 105 since purchase was created manually without incrementing stock)
        $this->product->refresh();
        $this->assertEquals(105, $this->product->quantity);
    }

    /**
     * Test deleting a single purchase item line.
     */
    public function test_delete_purchase_item_successfully()
    {
        $purchase = new Purchase();
        $purchase->invoice_no = 'PUR-001';
        $purchase->user_id = $this->user->id;
        $purchase->branch_id = $this->branch->id;
        $purchase->supplier_id = $this->supplier->id;
        $purchase->date = '2026-06-13';
        $purchase->custom_name = $this->supplier->name;
        $purchase->sub_total = 10000;
        $purchase->grand_total = 10000;
        $purchase->received = 0;
        $purchase->remained = 10000;
        $purchase->save();

        $purchaseitem = new Purchaseitem();
        $purchaseitem->product_id = $this->product->id;
        $purchaseitem->purchase_id = $purchase->id;
        $purchaseitem->code = $this->product->code;
        $purchaseitem->name = $this->product->name;
        $purchaseitem->quantity = 2;
        $purchaseitem->price = 5000;
        $purchaseitem->save();

        // Hit custom controller endpoint to delete purchase item
        $response = $this->actingAs($this->user)
            ->get(action([\App\Http\Controllers\CustomController::class, 'deletePurchaseItems'], ['id' => $purchaseitem->id]));

        $response->assertRedirect(route('platform.purchase.edit-custom', $purchase->id));

        // Assert item is deleted
        $this->assertDatabaseMissing('purchaseitems', ['id' => $purchaseitem->id]);

        // Assert totals are recalculated
        $purchase->refresh();
        $this->assertEquals(0, $purchase->sub_total);
        $this->assertEquals(0, $purchase->grand_total);

        // Assert product stock is updated (100 - 2 = 98)
        $this->product->refresh();
        $this->assertEquals(98, $this->product->quantity);
    }
}
