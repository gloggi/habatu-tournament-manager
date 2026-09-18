<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nickname']);
            $table->string('username')->nullable()->unique()->after('id');
        });

        // Backfill existing users: use midata_id as string if present, else nickname
        $users = DB::table('users')->whereNull('username')->get();
        foreach ($users as $user) {
            $username = !empty($user->midata_id) ? (string) $user->midata_id : $user->nickname;
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
            $table->unique('nickname');
        });
    }
};
