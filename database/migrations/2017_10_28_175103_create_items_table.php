<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->string('cost');
            $table->string('weight');
            $table->integer('arcane_focus_id');
            $table->integer('druidic_focus_id');
            $table->integer('holy_symbol_id');
            $table->timestamps();
			
			$table->foreign('arcane_focus_id')->references('id')->on('arcane_foci');
			$table->foreign('druidic_focus_id')->references('id')->on('druidic_foci');
			$table->foreign('holy_symbol_id')->references('id')->on('holy_symbols');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
