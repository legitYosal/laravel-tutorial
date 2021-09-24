<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLikeToPollymorphicRelationToPostAndProductRemovePost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            //
            $table->dropForeign(['post_id']);
            $table->dropColumn('post_id');

            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            //
            $table->foreignId('post_id')
                ->constrained('posts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->dropColumn('likeable_id');
            $table->dropColumn('likeable_type');
        });
    }
}
