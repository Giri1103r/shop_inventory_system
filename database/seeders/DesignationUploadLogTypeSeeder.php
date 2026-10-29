<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationUploadLogTypeSeeder extends Seeder
{
    /**
     * Registers the Designation sample file so that the import screen
     * (sample download + upload log) works like the other masters.
     */
    public function run(): void
    {
        $exists = DB::table('template_admin_upload_log_type')
            ->where('key_name', 'designation')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('template_admin_upload_log_type')->insert([
            'type_name' => 'Designation',
            'key_name' => 'designation',
            'sample_file' => 'public/assets/documents/excel/sample_upload/Designation.xlsx',
            'file_name' => 'Designation.xlsx',
            'created_by' => 0,
            'status' => 1,
            'trash' => 'NO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
