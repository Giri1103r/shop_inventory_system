<style>
    .back_button {
        background-color: #6b757d !important;
        color: #fff !important;
        border: none;
        font-weight: 500;
        border-radius: 6px;
        transition:
            background-color 0.3s ease,
            box-shadow 0.3s ease,
            transform 0.2s ease;
    }

    .back_button:hover {
        background-color: #545b62 !important; /* darker shade of grey */
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(0,0,0,0.25);
        transform: translateY(-2px);
    }

    .back_button:active {
        transform: translateY(0);
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }

    .btn-icon-start i {
        margin-right: 6px;
        transition: transform 0.3s ease;
    }

    .back_button:hover .btn-icon-start i {
        transform: scale(1.1);
    }

    .fa-upload {
        font-size: 15px;
    }
</style>

<a href="{{ $href }}" class="btn back_button back_button">
    <span>{{ __('common.back') }}</span>
</a>
