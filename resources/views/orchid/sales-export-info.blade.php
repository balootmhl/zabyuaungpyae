{{-- resources/views/orchid/sales-export-info.blade.php --}}

<div class="bg-light p-4 rounded mb-4">
    <h5 class="mb-3">
        <i class="icon-info text-info me-2"></i>
        Export Information
    </h5>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Sales</h6>
                    <h4 class="mb-0">{{ number_format($total_sales) }}</h4>
                    <small>All time records</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">This Month</h6>
                    <h4 class="mb-0">{{ number_format($this_month_sales) }}</h4>
                    <small>{{ now()->format('M Y') }} records</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Your Access</h6>
                    <h4 class="mb-0">{{ $user_branch }}</h4>
                    <small>{{ $is_admin ? 'Admin Access' : 'Branch Access' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-0">
        <h6 class="alert-heading">
            <i class="icon-bulb me-1"></i>
            Export Tips
        </h6>
        <ul class="mb-0">
            <li><strong>Date Range:</strong> Use the date picker to filter sales by specific dates</li>
            <li><strong>Export Formats:</strong>
                <ul>
                    <li><em>Detailed:</em> Complete sales data with all columns</li>
                    <li><em>Summary:</em> Key information only (faster download)</li>
                    <li><em>Items:</em> Product-focused export with item details</li>
                </ul>
            </li>
            <li><strong>Preview:</strong> Click "Preview Data" to see how many records will be exported</li>
            @if($is_admin)
            <li><strong>Branch Filter:</strong> As admin, you can export specific branches or all branches</li>
            @else
            <li><strong>Access:</strong> You can only export data from your assigned branch</li>
            @endif
        </ul>
    </div>
</div>
