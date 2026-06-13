<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomController;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Saleitem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $branch;
    protected $customer;
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
                'platform.module.sale' => true,
            ],
        ]);

        // Create customer
        $this->customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '099876543',
            'address' => 'Mandalay',
            'debt' => 0,
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
     * Test creating a sale invoice successfully.
     */
    public function test_store_sale_successfully()
    {
        $response = $this->actingAs($this->user)
            ->post(route('platform.sale.store-custom'), [
                'customer_id' => 'John Doe', // firstOrCreate by name
                'invoice_code' => '001',
                'is_inv_auto' => 0,
                'date' => '2026-06-13',
                'address' => 'Mandalay',
                'is_saleprice' => 1,
                'discount' => 1000,
                'received' => 5000,
                'remarks' => 'Test sale',
                'product' => $this->product->id,
                'price' => 7000,
                'qty' => 2,
            ]);

        // Assert redirect to edit page
        $sale = Sale::first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('platform.sale.edit-custom', $sale->id));

        // Assert sale details
        $this->assertEquals('#' . date('y') . date('m') . '001', $sale->invoice_no);
        $this->assertEquals(14000, $sale->sub_total); // 7000 * 2
        $this->assertEquals(13000, $sale->grand_total); // 14000 - 1000
        $this->assertEquals(8000, $sale->remained); // 13000 - 5000

        // Assert product quantity decremented
        $this->product->refresh();
        $this->assertEquals(98, $this->product->quantity);

        // Assert customer debt incremented by remained amount
        $customer = Customer::find($this->customer->id);
        $this->assertEquals(8000, $customer->debt);
    }

    /**
     * Test updating a sale invoice.
     */
    public function test_update_sale_successfully()
    {
        // First create a sale
        $sale = new Sale();
        $sale->invoice_no = 'INV-001';
        $sale->user_id = $this->user->id;
        $sale->branch_id = $this->branch->id;
        $sale->customer_id = $this->customer->id;
        $sale->date = '2026-06-13';
        $sale->custom_name = $this->customer->name;
        $sale->discount = 1000;
        $sale->sub_total = 14000;
        $sale->grand_total = 13000;
        $sale->received = 5000;
        $sale->remained = 8000;
        $sale->save();

        $saleitem = new Saleitem();
        $saleitem->product_id = $this->product->id;
        $saleitem->sale_id = $sale->id;
        $saleitem->code = $this->product->code;
        $saleitem->name = $this->product->name;
        $saleitem->quantity = 2;
        $saleitem->price = 7000;
        $saleitem->save();

        // Apply initial debt to customer
        $this->customer->debt = 8000;
        $this->customer->update();

        // Update the sale with a new item (total Qty 2 + 1 = 3)
        $response = $this->actingAs($this->user)
            ->post(route('platform.sale.update-custom'), [
                'sale_id' => $sale->id,
                'customer_id' => 'John Doe',
                'invoice_code' => '001',
                'is_inv_auto' => 0,
                'date' => '2026-06-13',
                'address' => 'Mandalay',
                'is_saleprice' => 1,
                'discount' => 2000, // Change discount
                'received' => 10000, // Change received
                'product' => $this->product->id,
                'price' => 7000,
                'qty' => 1, // Add 1 more product
            ]);

        $response->assertRedirect(route('platform.sale.edit-custom', $sale->id));

        $sale->refresh();
        $this->assertEquals(21000, $sale->sub_total); // (2+1) * 7000
        $this->assertEquals(19000, $sale->grand_total); // 21000 - 2000
        $this->assertEquals(9000, $sale->remained); // 19000 - 10000

        // Assert product stock updated
        $this->product->refresh();
        $this->assertEquals(99, $this->product->quantity); // Initial 100 - 2 - 1 = 97 (Wait, setUp had 100, but in test_update setUp is fresh, so 100 - 1 = 99 since $sale was created manually without decrementing stock)

        // Assert customer debt adjusted
        $this->customer->refresh();
        $this->assertEquals(9000, $this->customer->debt);
    }

    /**
     * Test deleting a sale invoice.
     */
    public function test_delete_sale_successfully()
    {
        $sale = new Sale();
        $sale->invoice_no = 'INV-001';
        $sale->user_id = $this->user->id;
        $sale->branch_id = $this->branch->id;
        $sale->customer_id = $this->customer->id;
        $sale->date = '2026-06-13';
        $sale->custom_name = $this->customer->name;
        $sale->sub_total = 7000;
        $sale->grand_total = 7000;
        $sale->received = 0;
        $sale->remained = 7000;
        $sale->save();

        $saleitem = new Saleitem();
        $saleitem->product_id = $this->product->id;
        $saleitem->sale_id = $sale->id;
        $saleitem->code = $this->product->code;
        $saleitem->name = $this->product->name;
        $saleitem->quantity = 1;
        $saleitem->price = 7000;
        $saleitem->save();

        $this->customer->debt = 7000;
        $this->customer->update();

        $response = $this->actingAs($this->user)
            ->post(route('platform.sale.delete-custom'), [
                'id' => $sale->id,
            ]);

        $response->assertRedirect(route('platform.sale.list'));

        // Assert sale is deleted
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseMissing('saleitems', ['id' => $saleitem->id]);

        // Assert product stock is returned
        $this->product->refresh();
        $this->assertEquals(101, $this->product->quantity); // 100 + 1 = 101

        // Assert customer debt is reverted
        $this->customer->refresh();
        $this->assertEquals(0, $this->customer->debt);
    }

    /**
     * Test deleting a single item line from an invoice.
     */
    public function test_delete_single_sale_item_successfully()
    {
        $sale = new Sale();
        $sale->invoice_no = 'INV-001';
        $sale->user_id = $this->user->id;
        $sale->branch_id = $this->branch->id;
        $sale->customer_id = $this->customer->id;
        $sale->date = '2026-06-13';
        $sale->custom_name = $this->customer->name;
        $sale->sub_total = 14000;
        $sale->grand_total = 14000;
        $sale->received = 5000;
        $sale->remained = 9000;
        $sale->save();

        $saleitem1 = new Saleitem();
        $saleitem1->product_id = $this->product->id;
        $saleitem1->sale_id = $sale->id;
        $saleitem1->code = $this->product->code;
        $saleitem1->name = $this->product->name;
        $saleitem1->quantity = 1;
        $saleitem1->price = 7000;
        $saleitem1->save();

        $saleitem2 = new Saleitem();
        $saleitem2->product_id = $this->product->id;
        $saleitem2->sale_id = $sale->id;
        $saleitem2->code = $this->product->code;
        $saleitem2->name = $this->product->name;
        $saleitem2->quantity = 1;
        $saleitem2->price = 7000;
        $saleitem2->save();

        $this->customer->debt = 9000;
        $this->customer->update();

        // Hit custom controller endpoint to delete sale item 1
        $response = $this->actingAs($this->user)
            ->get(action([\App\Http\Controllers\CustomController::class, 'deleteSaleItems'], ['id' => $saleitem1->id]));

        $response->assertRedirect(route('platform.sale.edit-custom', $sale->id));

        // Assert item 1 is deleted, item 2 remains
        $this->assertDatabaseMissing('saleitems', ['id' => $saleitem1->id]);
        $this->assertDatabaseHas('saleitems', ['id' => $saleitem2->id]);

        // Assert totals and remains are recalculated
        $sale->refresh();
        $this->assertEquals(7000, $sale->sub_total);
        $this->assertEquals(7000, $sale->grand_total);
        $this->assertEquals(2000, $sale->remained); // 7000 - 5000

        // Assert customer debt is recalculated
        $this->customer->refresh();
        $this->assertEquals(2000, $this->customer->debt);
    }

    /**
     * Test transactional rollback when store throws database exception.
     */
    public function test_store_sale_rollback_on_failure()
    {
        $response = $this->actingAs($this->user)
            ->post(route('platform.sale.store-custom'), [
                'customer_id' => 'John Doe',
                'invoice_code' => '001',
                'is_inv_auto' => 0,
                'date' => '2026-06-13',
                'product' => 999999, // INVALID PRODUCT ID to trigger exception
                'price' => 7000,
                'qty' => 2,
            ]);

        // Assert no sale is stored in the database
        $this->assertEquals(0, Sale::count());
        $this->assertEquals(0, Saleitem::count());

        // Assert redirect back with input
        $response->assertRedirect();
        $this->assertEquals(0, $this->customer->debt);
    }
}
