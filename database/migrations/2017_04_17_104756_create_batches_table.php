<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('file_name');
            $table->enum('status', ['Created', 'PartiallyAssigned', 'FullyAssigned', 'InProcess', 'Completed', 'Submitted', 'QCInProcess', 'Final']);
            $table->integer('upload_user_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('upload_user_id')->references('id')->on('users');
        });
        
        /**
        Schema::create('article_user', function (Blueprint $table) {
            $table->integer('article_id')->unsigned();
            $table->integer('user_id')->unsigned();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('article_id')->references('id')->on('articles');
            
            $table->primary(['user_id', 'article_id']);
        });
        **/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('batches');
    }
}
