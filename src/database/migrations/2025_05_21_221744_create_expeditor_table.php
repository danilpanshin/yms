<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expeditors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(User::class);
            $table->string('comment')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment() !== 'master') {
            $faker = FakerFactory::create('ru_RU'); // Russian data generator

            // Ensure user with id 3 exists
            if (User::where('id', 3)->exists()) {
                $drivers = [];

                for ($i = 0; $i < 20; $i++) {
                    $drivers[] = [
                        'name' => $faker->lastName . ' ' . $faker->firstName . ' ' . $faker->middleName,
                        'user_id' => 3,
                        'comment' => $faker->optional(0.7)->sentence,
                        'phone' => '+7' . $faker->numerify('9########'),
                        'email' => $faker->safeEmail,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('expeditors')->insert($drivers);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expeditor');
    }
};
