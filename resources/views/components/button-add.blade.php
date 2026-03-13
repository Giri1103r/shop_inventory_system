<style>
    .btn-add {
        background-color: #dc3545;
        color: #ffffff !important;
        border: none;
        font-weight: 500;
        border-radius: 6px;

        display: inline-flex;
        align-items: center;

        transition:
            background-color 0.3s ease,
            box-shadow 0.3s ease,
            transform 0.2s ease;
    }

    .btn-add:hover {
        background-color: #be2636 !important;
        color: #ffffff !important;

        box-shadow: 0 6px 16px rgba(158, 163, 234, 0.45);
        transform: translateY(-2px);
    }

    .btn-add:active {
        transform: translateY(0);
        box-shadow: 0 3px 8px rgba(158, 163, 234, 0.25);
    }

    .btn-add .fa-plus {
        font-size: 15px;
        margin-right: 6px;
        transition: transform 0.45s ease-in-out;
    }

    /* Rotate icon on hover */
    .btn-add:hover .fa-plus {
        transform: rotate(-90deg);
    }
</style>

<a href="{{ $href }}">
    <button class="btn btn-add  {{ $class }}">
        <i class="fe-plus-square me-1"></i> {{ __('common.add') }}
    </button>
</a>