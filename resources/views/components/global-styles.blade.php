<style>
    :root {
        --saas-surface: var(--bs-body-bg, #212529);
        --saas-muted: var(--bs-secondary-color, #94a3b8);
        --saas-line: var(--bs-border-color, #343a40);
        --saas-track: var(--bs-tertiary-bg, #2b3035);
        --saas-accent: var(--bs-primary, #344d90);
        --saas-radius: 16px;
    }

    body {
        background:
            radial-gradient(circle at 100% 0, rgba(var(--bs-primary-rgb), .035), transparent 28rem),
            var(--saas-surface);
        min-height: 100vh;
    }

    main.container-fluid {
        max-width: 1440px;
    }

    h1, h2, h3, h4, h5, h6 {
        letter-spacing: -.02em;
    }

    .card {
        border: 1px solid var(--saas-line) !important;
        border-radius: var(--saas-radius) !important;
        background: var(--saas-surface);
        box-shadow: 0 10px 28px rgba(0, 0, 0, .10) !important;
        overflow: hidden;
    }

    .card-header,
    .card-footer,
    .modal-header,
    .modal-footer {
        border-color: var(--saas-line) !important;
    }

    .card-header,
    .card-footer,
    .bg-body-tertiary {
        background: var(--saas-track) !important;
    }

    .card.bg-body-tertiary,
    .card.shadow-sm.bg-body-tertiary {
        background: var(--saas-surface) !important;
    }

    .card .card {
        box-shadow: none !important;
    }

    .btn {
        border-radius: 10px;
        min-height: 38px;
        font-weight: 600;
        transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease, border-color .16s ease;
    }

    .btn-sm {
        min-height: 34px;
        border-radius: 8px;
    }

    .btn-primary {
        border-color: var(--bs-primary);
        box-shadow: 0 7px 18px rgba(var(--bs-primary-rgb), .16);
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn:focus-visible,
    .form-control:focus,
    .form-select:focus,
    .form-check-input:focus {
        box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .13);
    }

    .form-control,
    .form-select,
    .input-group-text {
        border-color: var(--saas-line);
        border-radius: 10px;
        background-color: var(--saas-track);
        color: var(--bs-body-color);
    }

    .form-control-sm,
    .form-select-sm {
        border-radius: 8px;
    }

    .form-control::placeholder {
        color: var(--saas-muted);
    }

    .input-group > :not(:first-child) {
        border-left: 0;
    }

    .input-group > .btn {
        border-left: 0;
    }

    .form-label {
        color: var(--saas-muted);
    }

    .table {
        --bs-table-bg: var(--saas-surface);
        --bs-table-color: var(--bs-body-color);
        --bs-table-border-color: var(--saas-line);
        --bs-table-hover-bg: rgba(var(--bs-primary-rgb), .055);
        --bs-table-hover-color: var(--bs-body-color);
    }

    .table thead th {
        background: var(--saas-track);
        border-bottom: 1px solid var(--saas-line);
        color: var(--saas-muted);
    }

    .table td,
    .table th {
        border-color: var(--saas-line);
        padding-top: .9rem;
        padding-bottom: .9rem;
    }

    .badge {
        border-radius: 7px;
    }

    .badge.bg-secondary,
    .badge.bg-white,
    .badge.bg-body,
    .badge.bg-light {
        background: var(--saas-track) !important;
        color: var(--bs-body-color) !important;
        border: 1px solid var(--saas-line) !important;
    }

    .alert {
        border: 1px solid var(--saas-line);
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .09) !important;
    }

    .modal-content {
        border: 1px solid var(--saas-line) !important;
        border-radius: var(--saas-radius) !important;
        background: var(--saas-surface);
        box-shadow: 0 24px 60px rgba(0, 0, 0, .32);
    }

    .modal-backdrop {
        --bs-backdrop-opacity: .72;
    }

    .dropdown-menu {
        border: 1px solid var(--saas-line);
        border-radius: 12px;
        background: var(--saas-surface);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .22);
    }

    .dropdown-item {
        border-radius: 8px;
    }

    .nav-underline .nav-link {
        border-radius: 8px 8px 0 0;
    }

    .progress {
        background: var(--saas-track);
        border-radius: 999px;
    }

    .progress-bar {
        border-radius: inherit;
    }

    .pagination {
        gap: .35rem;
    }

    .pagination .page-link {
        border: 1px solid var(--saas-line);
        border-radius: 8px !important;
        background: var(--saas-surface);
        color: var(--bs-body-color);
    }

    .pagination .page-link:hover {
        background: var(--saas-track);
        color: var(--bs-primary);
    }

    .pagination .active > .page-link,
    .pagination .page-item.active .page-link {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }

    .text-dark,
    .text-muted,
    .text-body-tertiary {
        color: var(--saas-muted) !important;
    }

    .bg-white,
    .bg-light {
        background: var(--saas-track) !important;
    }

    .border-light,
    .border-secondary-subtle {
        border-color: var(--saas-line) !important;
    }

    .ts-wrapper .ts-control,
    .ts-wrapper .ts-dropdown {
        border-color: var(--saas-line);
        border-radius: 10px;
        background: var(--saas-track);
        color: var(--bs-body-color);
    }

    .ts-wrapper .ts-dropdown {
        box-shadow: 0 16px 36px rgba(0, 0, 0, .22);
    }

    .ts-wrapper .ts-dropdown .option.active,
    .ts-wrapper .ts-dropdown .option.selected {
        background: rgba(var(--bs-primary-rgb), .12);
        color: var(--bs-primary);
    }

    :focus-visible {
        outline: 2px solid rgba(var(--bs-primary-rgb), .75);
        outline-offset: 2px;
    }

    @media (max-width: 575.98px) {
        main.container-fluid,
        main.container-fluid.py-4 {
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .card {
            border-radius: 12px !important;
        }

        .btn {
            min-height: 42px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .btn,
        .card {
            transition: none;
        }
    }
</style>
