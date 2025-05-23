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
        Schema::create('acceptances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('comment')->nullable(true);
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment() !== 'master') {
            // Generate and insert data only if not in master environment
            $acceptances = [];
            $usedNumbers = [];

            for ($i = 1; $i <= 13; $i++) {
                do {
                    $number = rand(1, 100);
                } while (in_array($number, $usedNumbers));

                $usedNumbers[] = $number;

                $acceptances[] = [
                    'name' => 'Тип приемки ' . $number,
                    'comment' => rand(0, 1) ? 'Sample comment for acceptance ' . $number : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('acceptances')->insert($acceptances);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptances');
    }
};
