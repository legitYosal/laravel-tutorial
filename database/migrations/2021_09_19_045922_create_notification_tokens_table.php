<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('token');

            $table->foreignId('user_id') ->unique()
            ->constrained('users')
            ->onDelete('cascade')
            ->onUpdate('cascade')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_tokens');
    }
}
