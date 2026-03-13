<style>
    .btn-matching {
        background-color: #f9c306 !important;
        color: #fff !important;
        border: none;
        font-weight: 500;
        border-radius: 6px;
        transition:
            background-color 0.3s ease,
            box-shadow 0.3s ease,
            transform 0.2s ease;
    }

    .btn-matching:hover {
        background-color: #e0ae05 !important;
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(249, 195, 6, 0.35);
        transform: translateY(-2px);
    }

    .btn-matching:hover .btn-icon-start i {
        transform: rotate(-10deg) scale(1.1);
    }

    .btn-icon-start i {
        transition: transform 0.3s ease;
    }

    .btn-matching:active {
        transform: translateY(0);
        box-shadow: 0 3px 8px rgba(249, 195, 6, 0.25);
    }

    .fa-filter {
        font-size: 15px;
    }
</style>

<button data-bs-toggle="collapse" data-bs-target="#search" type="button"
    {{ $attributes->merge(['class' => 'btn btn-matching ' . $class]) }} {{ $attributes }}><span
        class="btn-icon-start text-secondary"><i class="fa fa-filter color-success"></i>
    </span>{{ __('common.filter') }}</button>
