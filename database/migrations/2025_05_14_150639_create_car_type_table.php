<?php

use App\Models\CarType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('comment')->nullable(true);
            $table->string('car_number')->nullable(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('car_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('comment')->nullable(true);
            $table->softDeletes();
            $table->timestamps();
        });

        $car_type_arr  = [
            'name' => 'Фура',
            'comment' => 'Sample comment for car_type ',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $car_type = new CarType();
        $car_type->forceFill($car_type_arr);
        $car_type->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_types');
        Schema::dropIfExists('car');
    }
};
