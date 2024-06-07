<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('serial_number');
            $table->string('model');
            $table->unsignedBigInteger('category_id'); // Assuming categories have their own table
            $table->decimal('sales_price', 8, 2); // For monetary values
            $table->unsignedBigInteger('unit_id'); // Assuming units have their own table
            $table->string('image')->nullable(); // Images might not be mandatory
            $table->unsignedBigInteger('tax_id'); // Assuming taxes have their own table
            $table->decimal('purchase_price', 8, 2)->nullable(); // For monetary values, might not always be known
            $table->date('purchase_date')->nullable(); // Dates should be stored properly
            $table->text('description')->nullable(); // Descriptions can be lengthy and optional
            $table->timestamps();

            // Set foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['tax_id']);
        });

        Schema::dropIfExists('products');
    }
}
