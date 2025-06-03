<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'stock_admin']);
        Role::create(['name' => 'supplier']);
        Role::create(['name' => 'driver']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::findByName("admin")->delete();
        Role::findByName("manager")->delete();
        Role::findByName("stock_admin")->delete();
        Role::findByName("supplier")->delete();
        Role::findByName("driver")->delete();
    }
};
