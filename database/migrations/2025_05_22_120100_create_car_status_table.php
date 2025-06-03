<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->softDeletes();
            $table->timestamps();
        });

        $statuses = [
            ['id' => 10, 'name' => 'Заявка оформлена', 'slug' => 'application_created'],
            ['id' => 20,'name' => 'На территории', 'slug' => 'on_site'],
            ['id' => 30,'name' => 'Разгрузка', 'slug' => 'unloading'],
            ['id' => 40,'name' => 'Выгружен', 'slug' => 'unloaded'],
            ['id' => 50,'name' => 'Машина уехала', 'slug' => 'left'],
            ['id' => 60,'name' => 'Отменено', 'slug' => 'canceled'],
        ];
        $add_statuses = [];
        foreach($statuses as $status){
            $add_statuses[] = [
                'id' => $status['id'],
                'name' => $status['name'],
                'slug' => $status['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('car_statuses')->insert($add_statuses);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_status');
    }
};
