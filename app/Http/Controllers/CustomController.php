<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Sale;
use App\Models\Saleitem;
// use Barryvdh\DomPDF\PDF;
use Orchid\Support\Facades\Alert;
use PDF;
use Illuminate\Http\Response;

class CustomController extends Controller
{
    public function deleteSaleItems($id)
    {
        $saleitem = Saleitem::findOrFail($id);
        $sale_id = $saleitem->sale_id;
        $product = Product::findOrFail($saleitem->product_id);
        $product->quantity = $product->quantity + $saleitem->quantity;
        $product->update();
        $saleitem->delete();
        Alert::info('You have successfully deleted a sale item.');
        return redirect()->route('platform.sale.edit', $sale_id);
    }

    public function downloadInvoice($id)
    {
        $sale = Sale::findOrFail($id);
        $pdf = PDF::loadView('export.salepdf', compact('sale'))->setPaper('a4');

        // return $pdf->download('invoice_' . $sale->invoice_no . '.pdf');

        return $pdf->stream('invoice_' . $sale->invoice_no . '.pdf');

        // return $pdf->download('invoice.pdf');
        // $output = $pdf->output();

        // return new Response($output, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Decomposition' => 'inline;filename="leepl.pdf"',
        // ]);
    }

    // For Purchase
    public function deletePurchaseItems($id)
    {
        $purchaseitem = Purchaseitem::findOrFail($id);
        $purchase_id = $purchaseitem->purchase_id;
        $product = Product::findOrFail($purchaseitem->product_id);
        $product->quantity = $product->quantity - $saleitem->quantity;
        $product->update();
        $purchaseitem->delete();
        Alert::info('You have successfully deleted a purchase item.');
        return redirect()->route('platform.purchase.edit', $purchase_id);
    }

    public function downloadPInvoice($id)
    {
        $purchase = Purchase::findOrFail($id);
        $pdf = PDF::loadView('export.purchasepdf', compact('purchase'))->setPaper('a4');

        return $pdf->stream('invoice_' . $purchase->invoice_no . '.pdf');
    }
}
