<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostPicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_pictures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('post_id')
                ->constrained('posts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->string('image_name');
            $table->string('image_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_pictures');
    }
}
