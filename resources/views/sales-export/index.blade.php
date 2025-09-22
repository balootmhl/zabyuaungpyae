@extends('platform::dashboard')

@section('title', 'Export Sales Data')
@section('description', 'Export sales invoices with date range and branch filters.')

@section('navbar')
    <div class="text-center">
        {{-- Additional navbar content if needed --}}
    </div>
@stop

@push('head')
    <style>
        .select2 {
            max-width: 100% !important;
        }
    </style>
@endpush

@section('content')

    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="exportForm" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="row justify-content-center invoice-form">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="date_start">Start Date</label>
                        <input type="date" class="form-control" name="date_start" id="date_start">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="date_end">End Date</label>
                        <input type="date" class="form-control" name="date_end" id="date_end">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="export_format">Export Format</label>
                        <select class="form-control" name="export_format" id="export_format" required>
                            <option value="detailed" selected>Detailed Export (All columns)</option>
                            <option value="summary">Summary Export (Key data only)</option>
                            <option value="items">Items Export (Product details)</option>
                        </select>
                    </div>
                </div>

                @if($user->id == 1)
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="branch_id">Select Branch</label>
                        <select class="form-control branch-select2" name="branch_id" id="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="info">Export Information</label>
                        <div class="bg-light p-2 rounded">
                            <small>
                                <strong>Total Sales:</strong> {{ number_format($totalSales) }}<br>
                                <strong>This Month:</strong> {{ number_format($thisMonthSales) }}<br>
                                <strong>Your Branch:</strong> {{ $user->branch->name ?? 'All Branches' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center invoice-form">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>Date Range</th>
                                    <th>Branch Filter</th>
                                    <th>Export Format</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="25%">
                                        <small class="text-muted">Choose the date range for sales export. Leave empty to export all dates.</small>
                                    </td>
                                    <td width="25%">
                                        @if($user->id == 1)
                                            <small class="text-muted">Admin can export any branch or all branches</small>
                                        @else
                                            <small class="text-muted">You can only export your branch data</small>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        <small class="text-muted">Choose what data to include in the export</small>
                                    </td>
                                    <td width="25%">
                                        <div class="toolbar">
                                            <button type="button" class="btn btn-outline-primary me-2" onclick="previewData()">
                                                Preview Data
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="exportData()">
                                                Export Excel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

@stop

@push('scripts')
    <script type="text/javascript">
        // activate select2 plugin
        $(document).ready(function() {
            $('.branch-select2').select2({
                placeholder: 'Select Branch',
                theme: "bootstrap"
            });
        });
    </script>
    <script>
        function previewData() {
            const form = document.getElementById('exportForm');
            form.action = '{{ route("sales.export.preview") }}';
            form.target = '_self';
            form.submit();
        }

        function exportData() {
            const form = document.getElementById('exportForm');
            form.action = '{{ route("sales.export.download") }}';
            form.target = '_blank';  // This opens in new tab
            form.submit();
        }
    </script>
@endpush
