<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->text('script')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->dropColumn('script');
        });
    }
};
