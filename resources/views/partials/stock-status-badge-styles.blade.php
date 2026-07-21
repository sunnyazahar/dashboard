<style>
    .stock-status-badge {
        display: inline-block;
        width: 92px;
        box-sizing: border-box;
        padding: 4px 8px !important;
        border: 1px solid transparent;
        border-radius: 4px;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 10px;
        text-align: center;
        white-space: nowrap;
    }
    label {
        margin-bottom:0px !important;
    }

    .stock-status-new {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #334155 !important;
    }

    .stock-status-stock {
        background-color: #70eab5 !important;
        color: #14532d !important;
    }

    .shipment-status-in-transit {
        background-color: #1b8bf9 !important;
        color: #ffffff !important;
    }

    .stock-status-in-progress {
        background-color: #f7f776 !important;
        color: #713f12 !important;
    }

    .stock-status-pending {
        background-color: #edbe9a !important;
        color: #5c2e0b !important;
    }

    .stock-status-cancelled {
        background-color: #e54a39 !important;
        color: #ffffff !important;
    }

    .stock-status-completed {
        background-color: #cfd1d0 !important;
        color: #374151 !important;
    }

    .stock-status-archived,
    .stock-status-unknown {
        background-color: #e2e8f0 !important;
        color: #475569 !important;
    }
</style>
<script>
    window.stockStatusBadgeClass = function(status) {
        var normalizedStatus = String(status || '').trim().toLowerCase();
        var classes = {
            'new': 'stock-status-new',
            'draft': 'stock-status-new',
            'stock': 'stock-status-stock',
            'in transit': 'shipment-status-in-transit',
            'in progress': 'stock-status-in-progress',
            'in process': 'stock-status-in-progress',
            'pending': 'stock-status-pending',
            'cancelled': 'stock-status-cancelled',
            'canceled': 'stock-status-cancelled',
            'completed': 'stock-status-completed',
            'delivered': 'stock-status-completed',
            'archived': 'stock-status-archived'
        };

        return classes[normalizedStatus] || 'stock-status-unknown';
    };
</script>
