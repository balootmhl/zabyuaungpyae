<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for Orchid to fully initialize
    setTimeout(function() {
        const exportButton = document.querySelector('[data-method="exportSalesRoute"]');

        if (exportButton) {
            console.log('Export button found, adding custom handler');

            // Remove existing event listeners by cloning the node
            const newButton = exportButton.cloneNode(true);
            exportButton.parentNode.replaceChild(newButton, exportButton);

            // Add our custom click handler
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                console.log('Export button clicked');

                // Get form data
                const form = document.querySelector('form');
                if (!form) {
                    alert('Form not found');
                    return;
                }

                // Get form field values directly
                const dateRangeStart = form.querySelector('[name="date_range[start]"]')?.value || '';
                const dateRangeEnd = form.querySelector('[name="date_range[end]"]')?.value || '';
                const branchId = form.querySelector('[name="branch_id"]')?.value || '';
                const exportFormat = form.querySelector('[name="export_format"]')?.value || 'detailed';

                // Build URL parameters
                const params = new URLSearchParams();
                if (dateRangeStart) params.append('date_range[start]', dateRangeStart);
                if (dateRangeEnd) params.append('date_range[end]', dateRangeEnd);
                if (branchId) params.append('branch_id', branchId);
                if (exportFormat) params.append('export_format', exportFormat);

                // Build the full URL
                const baseUrl = '{{ route("platform.sales.export.file") }}';
                const fullUrl = baseUrl + (params.toString() ? '?' + params.toString() : '');

                console.log('Opening URL:', fullUrl);

                // Open in new tab
                window.open(fullUrl, '_blank');

                return false;
            });
        } else {
            console.log('Export button not found');
        }
    }, 1000); // Wait 1 second for Orchid to fully load
});
</script>
