<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailboxes', function (Blueprint $table) {
            $table->bigInteger('user_id')->after('id')->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('maillists', function (Blueprint $table) {
            $table->bigInteger('user_id')->after('id')->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailboxes');
        Schema::dropIfExists('maillists');
    }
}
