<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['setting_key' => 167],
            ['setting_value' => '1']
        );

        DB::table('settings')->updateOrInsert(
            ['setting_key' => 168],
            ['setting_value' => '/assets/admin/images/svgs/shopping-cart.svg']
        );
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('setting_key', [167, 168])->delete();
    }
};
