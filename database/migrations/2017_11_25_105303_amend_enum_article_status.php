<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmendEnumArticleStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM ('UnAssigned', 'Assigned', 'Saved', 'Completed', 'Review', 'QualityChecked', 'EditsCompleted')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM ('UnAssigned', 'Assigned', 'Saved', 'Completed', 'Review', 'QualityChecked')");
    }
}
