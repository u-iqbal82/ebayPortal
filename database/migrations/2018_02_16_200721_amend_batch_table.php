<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmendBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->enum('status_final', 
                        ['Created', 'PartiallyAssigned', 'FullyAssigned', 
                        'InProcess', 'Completed', 'Submitted', 'QCInProcess', 'Final', 'Archived'])
                    ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
