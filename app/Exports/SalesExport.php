<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    protected $branch_id;
    protected $date_range;
    protected $export_format;
    protected $collection;

    public function __construct($branch_id = null, $date_range = null, $export_format = 'detailed')
    {
        $this->branch_id = $branch_id;
        $this->date_range = $date_range;
        $this->export_format = $export_format;
    }

    /**
     * Prepare the collection based on the export format
     */
    public function collection()
    {
        // For 'detailed' and 'summary' formats, we export sales
        if (in_array($this->export_format, ['detailed', 'summary'])) {
            $query = Sale::with(['customer', 'branch', 'saleitems.product'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($this->branch_id) {
                $query->where('branch_id', $this->branch_id);
            }
            if ($this->date_range && isset($this->date_range['start']) && isset($this->date_range['end'])) {
                $startDate = Carbon::parse($this->date_range['start'])->startOfDay();
                $endDate = Carbon::parse($this->date_range['end'])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            return $query->get();
        }

        // For 'items' format, we need to export SaleItem data, not Sale data.
        // We will create a flat collection of sale items.
        if ($this->export_format === 'items') {
            $query = SaleItem::with(['sale.customer', 'sale.branch', 'product']);

            // Filter by Sale's branch_id and created_at
            $query->whereHas('sale', function (Builder $saleQuery) {
                if ($this->branch_id) {
                    $saleQuery->where('branch_id', $this->branch_id);
                }
                if ($this->date_range && isset($this->date_range['start']) && isset($this->date_range['end'])) {
                    $startDate = Carbon::parse($this->date_range['start'])->startOfDay();
                    $endDate = Carbon::parse($this->date_range['end'])->endOfDay();
                    $saleQuery->whereBetween('created_at', [$startDate, $endDate]);
                }
            });

            // Order by the sale's created_at date
            $query->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                  ->orderBy('sales.created_at', 'desc')
                  ->select('sale_items.*'); // Select back all sale_items columns

            return $query->get();
        }

        return collect();
    }

    /**
     * Map each item to export format
     */
    public function map($item): array
    {
        switch ($this->export_format) {
            case 'summary':
                // Item is a Sale model
                return $this->mapSummary($item);
            case 'items':
                // Item is a SaleItem model
                return $this->mapItems($item);
            default:
                // Item is a Sale model
                return $this->mapDetailed($item);
        }
    }

    /**
     * Detailed export mapping (for Sale model)
     */
    private function mapDetailed($sale): array
    {
        return [
            $sale->id,
            $sale->invoice_no ?? 'N/A',
            $sale->customer->name ?? 'N/A',
            $sale->customer->phone ?? 'N/A',
            $sale->customer->email ?? 'N/A',
            $sale->branch->name ?? 'N/A',
            $sale->date ?? $sale->created_at->format('Y-m-d'),
            $sale->sub_total ?? 0,
            $sale->discount ?? 0,
            $sale->grand_total ?? 0,
            $sale->paid ?? 0,
            $sale->due ?? 0,
            $sale->payment_method ?? 'N/A',
            $sale->note ?? '',
            $this->getItemsDetails($sale),
            $sale->saleitems->count(),
            $sale->created_at->format('Y-m-d H:i:s'),
            $sale->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Summary export mapping (for Sale model)
     */
    private function mapSummary($sale): array
    {
        return [
            $sale->id,
            $sale->invoice_no ?? 'N/A',
            $sale->customer->name ?? 'N/A',
            $sale->branch->name ?? 'N/A',
            $sale->date ?? $sale->created_at->format('Y-m-d'),
            $sale->grand_total ?? 0,
            $sale->payment_method ?? 'N/A',
            $sale->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Items-focused export mapping (for SaleItem model)
     */
    private function mapItems($item): array
    {
        // Now $item is a SaleItem model, not a Sale model
        $sale = $item->sale;
        $product = $item->product;

        return [
            $sale->id,
            $sale->invoice_no ?? 'N/A',
            $sale->customer->name ?? 'N/A',
            $sale->date ?? $sale->created_at->format('Y-m-d'),
            $product->name ?? 'Unknown Product',
            $product->code ?? 'N/A',
            $item->quantity,
            $item->price,
            $item->quantity * $item->price,
            $sale->created_at->format('Y-m-d H:i:s'),
        ];
    }

    // The rest of your methods (`getItemsDetails`, `headings`, `columnFormats`, `styles`) are fine.
    // They should be placed below this section.
    // I am including them here for completeness.

    /**
     * Get items details for a sale
     */
    private function getItemsDetails($sale)
    {
        $items = [];
        foreach ($sale->saleitems as $item) {
            $items[] = ($item->product->name ?? 'Unknown') .
                       ' (Qty: ' . $item->quantity . ', Price: ' . $item->price . ')';
        }
        return implode('; ', $items);
    }

    /**
     * Define headings based on export format
     */
    public function headings(): array
    {
        switch ($this->export_format) {
            case 'summary':
                return [
                    'ID',
                    'Invoice No',
                    'Customer Name',
                    'Branch',
                    'Date',
                    'Total Amount',
                    'Payment Method',
                    'Created At',
                ];
            case 'items':
                return [
                    'Sale ID',
                    'Invoice No',
                    'Customer Name',
                    'Date',
                    'Product Name',
                    'Product Code',
                    'Quantity',
                    'Unit Price',
                    'Total Price',
                    'Created At',
                ];
            default:
                return [
                    'ID',
                    'Invoice No',
                    'Customer Name',
                    'Customer Phone',
                    'Customer Email',
                    'Branch',
                    'Date',
                    'Sub Total',
                    'Discount',
                    'Grand Total',
                    'Paid',
                    'Due',
                    'Payment Method',
                    'Note',
                    'Items Details',
                    'Items Count',
                    'Created At',
                    'Updated At',
                ];
        }
    }

    /**
     * Format columns
     */
    public function columnFormats(): array
    {
        switch ($this->export_format) {
            case 'summary':
                return [
                    'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Amount
                ];
            case 'items':
                return [
                    'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Unit Price
                    'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Price
                ];
            default:
                return [
                    'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Sub Total
                    'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Discount
                    'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Grand Total
                    'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Paid
                    'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Due
                ];
        }
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],

            // Auto-size columns
            'A:Z' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
