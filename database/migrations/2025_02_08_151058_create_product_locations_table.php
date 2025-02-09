<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('product_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // علاقة بالـ products
            $table->string('location');
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->unique(['product_id', 'location']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_locations');
    }
};

