<style>
    .btn-match {
        background-color: #30c230 !important;
        color: #fff !important;
        border: none;
        font-weight: 500;
        border-radius: 6px;

        transition:
            background-color 0.3s ease,
            box-shadow 0.3s ease,
            transform 0.2s ease;
    }

    .btn-match:hover {
        background-color: rgb(104, 199, 116) !important;
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(48, 194, 48, 0.35);
        transform: translateY(-2px);
    }

    .btn-match:active {
        transform: translateY(0);
        box-shadow: 0 3px 8px rgba(48, 194, 48, 0.25);
    }

    .btn-icon-start i {
        margin-right: 6px;
        transition: transform 0.3s ease;
    }

    .btn-match:hover .btn-icon-start i {
        transform: scale(1.1);
    }

    .fa-upload {
        font-size: 15px;
    }
</style>

<a href="{{ $href }}" class="btn btn-match">
    <span class="btn-icon-start">
        <i class="fa fa-upload"></i>
    </span>
    <span>{{ __('common.import') }}</span>
</a>
