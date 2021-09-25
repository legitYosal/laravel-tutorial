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

            $table->renameColumn('post_id', 'likeable_id');
            $table->string('likeable_type')->default(
                \App\Models\Post::getLikeableTypeStr()
            );
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
            $table->renameColumn('likeable_id', 'post_id');
            $table->dropColumn('likeable_type');
            
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }
}