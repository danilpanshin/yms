<?php

use App\Models\Acceptance;
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

        foreach($acceptances as $acceptance) {
            $acceptance_obj = new Acceptance();
            $acceptance_obj->forceFill([
                'name' => $acceptance['name'],
                'comment' => $acceptance['comment'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $acceptance_obj->save();
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
