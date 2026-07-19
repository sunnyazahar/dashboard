<style>
    .filter-group,
    .filter-item {
        overflow: visible !important;
    }

    .searchable-filter-wrapper {
        flex: 1;
        min-width: 0;
    }

    .searchable-filter-wrapper .btn-group {
        width: 100%;
    }

    .searchable-filter-wrapper .multiselect {
        width: 100%;
        height: 30px;
        padding: 4px 26px 4px 10px;
        overflow: hidden;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fff;
        color: #1e293b;
        font-size: 11px;
        text-align: left;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .filter-group .searchable-filter-wrapper .multiselect,
    .filter-item .searchable-filter-wrapper .multiselect {
        border: 0;
        border-radius: 0;
    }

    .searchable-filter-wrapper .multiselect-container {
        min-width: 280px;
        max-height: 420px;
        overflow-y: auto;
        padding: 6px 0;
        z-index: 1050;
    }

    .searchable-filter-wrapper .multiselect-container .input-group {
        width: calc(100% - 12px);
        margin: 0 6px 6px;
    }

    .searchable-filter-wrapper .multiselect-container label {
        padding-top: 7px;
        padding-bottom: 7px;
        color: #263238;
        font-size: 12px;
        white-space: normal;
    }

    .searchable-filter-wrapper .multiselect-container input[type="checkbox"] {
        margin-right: 8px;
        accent-color: #176b87;
    }

    .searchable-filter-wrapper .multiselect-reset a {
        color: #176b87;
        font-weight: 600;
    }
</style>
