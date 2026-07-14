<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('reminder_24h_sent')->default(false)->after('status');
            $table->boolean('reminder_6h_sent')->default(false)->after('reminder_24h_sent');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['reminder_24h_sent', 'reminder_6h_sent']);
        });
    }
};