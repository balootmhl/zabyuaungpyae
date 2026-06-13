<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Saleitem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyProfitReportTest extends TestCase
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

        // Create user with Orchid screen permissions
        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'permissions' => [
                'platform.index' => true,
                'platform.module.calculate-amount' => true,
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

        // Create product (cost/buy_price = 5000, sale_price = 7000)
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
     * Test loading the daily profit report screen and verifying profit calculations.
     */
    public function test_daily_profit_calculations_and_filters()
    {
        $todayDate = now()->format('Y-m-d');

        // 1. Create a sale matching today's date
        $sale = new Sale();
        $sale->invoice_no = 'INV-001';
        $sale->user_id = $this->user->id;
        $sale->branch_id = $this->branch->id;
        $sale->customer_id = $this->customer->id;
        $sale->date = $todayDate;
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

        // 2. Request the screen via GET (simulate page load)
        $response = $this->actingAs($this->user)
            ->get(route('platform.income.discount', [
                'date' => $todayDate,
                'branch_id' => $this->branch->id,
                'customer_id' => $this->customer->id,
            ]));

        $response->assertStatus(200);

        // Verify key layout text, customer, invoice no, revenue and profit calculations are outputted in the view
        $response->assertSee('John Doe');
        $response->assertSee('INV-001');
        $response->assertSee('13,000 Ks'); // total_revenue formatted
        $response->assertSee('4,000 Ks');  // total_profit formatted
        $response->assertSee('1');         // total_invoices count
    }

    /**
     * Test daily profit form filter redirect.
     */
    public function test_daily_profit_filter_redirects_correctly()
    {
        $response = $this->actingAs($this->user)
            ->post(route('platform.income.discount', ['method' => 'calculate']), [
                'date' => '2026-06-13',
                'branch_id' => $this->branch->id,
                'customer_id' => $this->customer->id,
            ]);

        $response->assertRedirect(route('platform.income.discount', [
            'date' => '2026-06-13',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
        ]));
    }
}
