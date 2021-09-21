<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->string('name', 50);
            $table->string('description', 255);
            $table->string('code', 10)->unique();
            $table->dateTime('added_at')->nullable();
            $table->dateTime('discontinued_at')->nullable();
            $table->decimal('price', 7, 2)->unsigned()->nullable();
            $table->unsignedSmallInteger('stoke_level')->default(0);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
