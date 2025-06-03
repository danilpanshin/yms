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
            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $acceptances = [
            ['name' => 'Региональная', 'comment' => ''],
            ['name' => 'Импортная', 'comment' => ''],
        ];

        $add_acceptances = [];

        foreach($acceptances as $acceptance) {
            $add_acceptances[] = [
                'name' => $acceptance['name'],
                'comment' => $acceptance['comment'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('acceptances')->insert($add_acceptances);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptances');
    }
};
