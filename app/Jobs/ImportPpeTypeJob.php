<?php

namespace App\Jobs;

use App\Models\Master\PpeType;
use App\Models\UploadLog;
use App\Models\UploadLogError;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Shuchkin\SimpleXLSX;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
class ImportPpeTypeJob implements ShouldQueue
// class ImportPpeTypeJob
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */

    protected $details;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {

        $this->details = $details;
    }

}
