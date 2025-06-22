<?php

use App\Models\LastSynced;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('last_synced', function (Blueprint $table) {
            $table->id();
            $table->dateTime('last_updated_at_supplier_transport');
            $table->timestamps();
        });

        $new = new LastSynced;
        $new->forceFill([
            'last_updated_at_supplier_transport' => now()->addMinutes(-10)
        ]);
        $new->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_synced');
    }
};
