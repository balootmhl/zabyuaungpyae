@php
    $totalRevenue = $summary['total_revenue'] ?? 0;
    $totalProfit = $summary['total_profit'] ?? 0;
    $totalMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
    $profitColorClass = $totalProfit > 0 ? 'text-profit-green' : ($totalProfit < 0 ? 'text-profit-red' : 'text-profit-muted');
@endphp

<div class="daily-profit-container mt-4">
    <!-- Custom CSS Styles -->
    <style>
        .daily-profit-container {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        
        /* Summary Cards Grid */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        }
        
        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .card-title-muted {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .card-icon-wrapper {
            background: #f1f5f9;
            color: #475569;
            padding: 0.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }
        
        .summary-card:hover .card-icon-wrapper {
            background: #e2e8f0;
            color: #0f172a;
        }
        
        .card-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }
        
        .card-desc {
            font-size: 0.75rem;
            color: #64748b;
        }

        .text-profit-green {
            color: #10b981 !important;
        }

        .text-profit-red {
            color: #ef4444 !important;
        }

        .text-profit-muted {
            color: #94a3b8 !important;
        }

        .bg-profit-green {
            background: #ecfdf5 !important;
            color: #10b981 !important;
        }

        .bg-profit-red {
            background: #fef2f2 !important;
            color: #ef4444 !important;
        }

        .bg-profit-muted {
            background: #f8fafc !important;
            color: #94a3b8 !important;
        }
        
        /* Table Card */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .table-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-outline-custom {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            font-size: 0.8125rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .btn-outline-custom:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }
        
        /* Table styles */
        .profit-table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        
        .profit-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        
        .profit-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.8125rem;
            padding: 0.875rem 1rem;
            border-bottom: 2px solid #f1f5f9;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        
        .profit-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            vertical-align: middle;
            color: #334155;
        }
        
        .invoice-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .invoice-row:hover {
            background-color: #f8fafc;
        }
        
        .invoice-row.expanded {
            background-color: #f1f5f9;
            border-bottom-color: #cbd5e1;
        }
        
        /* Details items row */
        .items-detail-row {
            background-color: #f8fafc;
        }
        
        .items-table-wrapper {
            padding: 0.75rem 1.5rem 1.5rem 2.5rem;
        }
        
        .inner-items-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .inner-items-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-bottom: 1px solid #e2e8f0;
            text-transform: capitalize;
        }
        
        .inner-items-table td {
            padding: 0.75rem 0.875rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8125rem;
        }
        
        /* Utilities */
        .font-mono-bold {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-weight: 700;
        }
        
        .align-right {
            text-align: right;
        }
        
        .chevron-icon {
            transition: transform 0.2s ease-in-out;
            color: #94a3b8;
        }
        
        .rotated-chevron {
            transform: rotate(90deg);
            color: #475569;
        }
        
        .badge-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            text-transform: capitalize;
            line-height: 1;
        }
        
        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-partial {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-secondary {
            background-color: #e2e8f0;
            color: #475569;
        }
        
        .product-code-tag {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.125rem 0.375rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-family: monospace;
            color: #475569;
            margin-right: 0.5rem;
            display: inline-block;
        }
        
        .warning-text {
            color: #d97706;
            font-size: 0.6875rem;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: 2px;
        }

        .empty-state-card {
            padding: 4rem 2rem;
            text-align: center;
            color: #64748b;
        }

        .empty-state-card svg {
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
    </style>

    <!-- Cards Summary Section -->
    <div class="summary-cards">
        <!-- Card 1: Total Invoices -->
        <div class="summary-card">
            <div class="card-header-flex">
                <span class="card-title-muted">Total Invoices</span>
                <div class="card-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg>
                </div>
            </div>
            <div class="card-value">{{ $summary['total_invoices'] }}</div>
            <div class="card-desc">Transactions for {{ $date }}</div>
        </div>

        <!-- Card 2: Total Revenue -->
        <div class="summary-card">
            <div class="card-header-flex">
                <span class="card-title-muted">Total Revenue</span>
                <div class="card-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="card-value">{{ number_format($totalRevenue) }} Ks</div>
            <div class="card-desc">Gross sales amount</div>
        </div>

        <!-- Card 3: Total Profit -->
        <div class="summary-card">
            <div class="card-header-flex">
                <span class="card-title-muted">Total Profit</span>
                <div class="card-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                </div>
            </div>
            <div class="card-value {{ $profitColorClass }}">{{ number_format($totalProfit) }} Ks</div>
            <div class="card-desc">Margin: {{ number_format($totalMargin, 1) }}%</div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h3 class="table-card-title">Invoices &amp; Profit Breakdown</h3>
            <div class="action-buttons">
                <button type="button" class="btn-outline-custom" id="expand-all-btn">
                    Expand All
                </button>
                <button type="button" class="btn-outline-custom" id="collapse-all-btn">
                    Collapse All
                </button>
            </div>
        </div>
        <div class="profit-table-responsive">
            <table class="profit-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th class="align-right">Revenue</th>
                        <th class="align-right">Profit</th>
                        <th class="align-right">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($invoices) === 0)
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-card">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                                    <p>No sales found for {{ $date }}</p>
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach($invoices as $invoice)
                            @php
                                $invRevenue = $invoice->grand_total;
                                $invProfit = $invoice->invoice_profit ?? 0;
                                $invMargin = $invRevenue > 0 ? ($invProfit / $invRevenue) * 100 : 0;
                                $invProfitColorClass = $invProfit > 0 ? 'text-profit-green' : ($invProfit < 0 ? 'text-profit-red' : 'text-profit-muted');
                                
                                // Compute payment status
                                $status = strtolower($invoice->status ?? '');
                                if (empty($status)) {
                                    if ($invoice->remained == 0) {
                                        $status = 'paid';
                                    } elseif ($invoice->received == 0 || $invoice->remained == $invoice->grand_total) {
                                        $status = 'unpaid';
                                    } else {
                                        $status = 'partial';
                                    }
                                }
                                
                                // Payment status badge classes
                                $badgeClass = 'badge-secondary';
                                if ($status === 'paid') $badgeClass = 'badge-paid';
                                elseif ($status === 'unpaid') $badgeClass = 'badge-unpaid';
                                elseif ($status === 'partial') $badgeClass = 'badge-partial';
                            @endphp
                            
                            <!-- Invoice Row -->
                            <tr class="invoice-row" data-target="items-row-{{ $invoice->id }}">
                                <td>
                                    <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </td>
                                <td class="font-mono-bold">{{ $invoice->invoice_no }}</td>
                                <td>{{ $invoice->customer->name ?? ($invoice->custom_name ?? '—') }}</td>
                                <td style="color: #64748b; font-size: 0.75rem;">{{ $invoice->branch->name ?? '—' }}</td>
                                <td>
                                    <span class="badge-pill {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="align-right font-mono-bold">{{ number_format($invRevenue) }} Ks</td>
                                <td class="align-right font-mono-bold {{ $invProfitColorClass }}">{{ number_format($invProfit) }} Ks</td>
                                <td class="align-right {{ $invProfitColorClass }}">{{ number_format($invMargin, 1) }}%</td>
                            </tr>

                            <!-- Line Items (Hidden by Default) -->
                            <tr id="items-row-{{ $invoice->id }}" class="items-detail-row" style="display: none;">
                                <td colspan="8">
                                    <div class="items-table-wrapper">
                                        <table class="inner-items-table">
                                            <thead>
                                                <tr>
                                                    <th>Product Info</th>
                                                    <th>Quantity</th>
                                                    <th>Cost Price</th>
                                                    <th class="align-right">Revenue</th>
                                                    <th class="align-right">Profit</th>
                                                    <th class="align-right">Margin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($invoice->saleitems) === 0)
                                                    <tr>
                                                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                                                            No line items found for this invoice.
                                                        </td>
                                                    </tr>
                                                @else
                                                    @foreach($invoice->saleitems as $item)
                                                        @php
                                                            $sellingPrice = $item->price ?? ($item->product->sale_price ?? 0);
                                                            $costPrice = $item->product->buy_price ?? 0;
                                                            $itemSubtotal = $sellingPrice * $item->quantity;
                                                            $profitPerUnit = $sellingPrice - $costPrice;
                                                            $itemProfit = $profitPerUnit * $item->quantity;
                                                            $itemMargin = $itemSubtotal > 0 ? ($itemProfit / $itemSubtotal) * 100 : 0;
                                                            
                                                            $itemProfitColor = $itemProfit > 0 ? 'text-profit-green' : ($itemProfit < 0 ? 'text-profit-red' : 'text-profit-muted');
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="product-code-tag">{{ $item->product->code ?? '—' }}</span>
                                                                <span style="font-weight: 600;">{{ $item->product->name ?? ($item->name ?? '—') }}</span>
                                                            </td>
                                                            <td>
                                                                <div style="display: flex; flex-direction: column;">
                                                                    <span>Qty: {{ $item->quantity }}</span>
                                                                    <span style="color: #64748b; font-size: 0.75rem;">@ {{ number_format($sellingPrice) }} Ks</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="display: flex; flex-direction: column;">
                                                                    <span>Cost: {{ number_format($costPrice) }} Ks</span>
                                                                    @if($costPrice == 0)
                                                                        <span class="warning-text">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12" y1="17" y2="17"/></svg> No cost found
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="align-right font-mono-bold">{{ number_format($itemSubtotal) }} Ks</td>
                                                            <td class="align-right font-mono-bold {{ $itemProfitColor }}">{{ number_format($itemProfit) }} Ks</td>
                                                            <td class="align-right {{ $itemProfitColor }}">{{ number_format($itemMargin, 1) }}%</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Interaction Scripts -->
<script>
    (function() {
        function initDailyProfitReport() {
            const container = document.querySelector('.daily-profit-container');
            if (!container || container.dataset.initialized) return;
            container.dataset.initialized = 'true';

            // Toggle individual row
            container.addEventListener('click', (e) => {
                const invoiceRow = e.target.closest('.invoice-row');
                if (invoiceRow && !e.target.closest('a, button, input, select')) {
                    const targetId = invoiceRow.dataset.target;
                    const targetRow = document.getElementById(targetId);
                    const chevron = invoiceRow.querySelector('.chevron-icon');
                    
                    if (targetRow) {
                        const isHidden = targetRow.style.display === 'none' || targetRow.style.display === '';
                        if (isHidden) {
                            targetRow.style.display = 'table-row';
                            invoiceRow.classList.add('expanded');
                            if (chevron) chevron.classList.add('rotated-chevron');
                        } else {
                            targetRow.style.display = 'none';
                            invoiceRow.classList.remove('expanded');
                            if (chevron) chevron.classList.remove('rotated-chevron');
                        }
                    }
                }
            });

            // Expand All
            const expandBtn = container.querySelector('#expand-all-btn');
            if (expandBtn) {
                expandBtn.addEventListener('click', () => {
                    container.querySelectorAll('.items-detail-row').forEach(row => {
                        row.style.display = 'table-row';
                    });
                    container.querySelectorAll('.invoice-row').forEach(row => {
                        row.classList.add('expanded');
                    });
                    container.querySelectorAll('.chevron-icon').forEach(chevron => {
                        chevron.classList.add('rotated-chevron');
                    });
                });
            }

            // Collapse All
            const collapseBtn = container.querySelector('#collapse-all-btn');
            if (collapseBtn) {
                collapseBtn.addEventListener('click', () => {
                    container.querySelectorAll('.items-detail-row').forEach(row => {
                        row.style.display = 'none';
                    });
                    container.querySelectorAll('.invoice-row').forEach(row => {
                        row.classList.remove('expanded');
                    });
                    container.querySelectorAll('.chevron-icon').forEach(chevron => {
                        chevron.classList.remove('rotated-chevron');
                    });
                });
            }
        }

        // Initialize on DOM load
        if (document.readyState !== 'loading') {
            initDailyProfitReport();
        } else {
            document.addEventListener('DOMContentLoaded', initDailyProfitReport);
        }
        
        // Support Orchid AJAX PJAX / Turbo loads
        document.addEventListener('pjax:success', initDailyProfitReport);
        document.addEventListener('turbo:load', initDailyProfitReport);
    })();
</script>
