<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersAddBlocked extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('linkedin_id', 'facebook_id');
            $table->boolean('blocked')->default(0)->after('is_advocate');
            $table->unsignedBigInteger('advocate_user_id')->nullable()->after('blocked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('facebook_id', 'linkedin_id');
            $table->dropColumn('blocked');
            $table->dropColumn('advocate_user_id');
        });
    }
}
