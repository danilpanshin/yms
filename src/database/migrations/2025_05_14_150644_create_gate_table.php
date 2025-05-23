<?php

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
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('comment')->nullable(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('gbort')->nullable(false)->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment() !== 'master') {
            // Generate and insert data only if not in master environment
            $gates = [];
            $usedNumbers = [];

            for ($i = 1; $i <= 13; $i++) {
                do {
                    $number = rand(1, 100);
                } while (in_array($number, $usedNumbers));

                $usedNumbers[] = $number;
                $isActive = rand(1, 100) > 20;

                $gates[] = [
                    'number' => (string)$number,
                    'name' => 'Gate ' . $number,
                    'comment' => rand(0, 1) ? 'Sample comment for gate ' . $number : null,
                    'is_active' => $isActive,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('gates')->insert($gates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};
