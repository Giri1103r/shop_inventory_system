<?php

return [
    App\Providers\AppServiceProvider::class,

    /*
    * Package Service Providers...
    */
    Yajra\DataTables\DataTablesServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    Kreait\Laravel\Firebase\ServiceProvider::class,

    /*
    * HMVC Service Providers...
    */
    App\Providers\GlobalServiceProvider::class,
];
