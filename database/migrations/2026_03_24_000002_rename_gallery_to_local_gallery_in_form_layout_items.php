<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('form_layout_items')
            ->where('element_type', 'field')
            ->where('element_key', 'gallery')
            ->update(['element_key' => 'local_gallery']);
    }

    public function down(): void
    {
        DB::table('form_layout_items')
            ->where('element_type', 'field')
            ->where('element_key', 'local_gallery')
            ->update(['element_key' => 'gallery']);
    }
};
