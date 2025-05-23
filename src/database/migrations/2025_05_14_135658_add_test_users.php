<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = ['admin', 'supplier', 'driver', 'manager', 'manager_admin'];

        $path = dirname(__FILE__) . '/../../.ht.pass';

        $pass_arr = [];
        if(file_exists($path)){
            $pass_arr = @unserialize(file_get_contents($path));
            if(!is_array($pass_arr)){
                $pass_arr = [];
            }
        }

        $output = new ConsoleOutput();

        foreach ($users as $user_row) {
            if(!isset($pass_arr[$user_row])){
                $pass = Str::random(32);
                $output->writeln('<info>' . $user_row . ' pass: ' . $pass . '</info>');
                $pass = Hash::make($pass);
                $pass_arr[$user_row] = $pass;
            } else {
                $pass = $pass_arr[$user_row];
            }

            $user = new App\Models\User();
            $user->password = $pass;
            $user->email = $user_row . '_@3l.ru';
            $user->name = $user_row;
            $user->save();

            $user->assignRole($user_row);
        }

        file_put_contents($path, serialize($pass_arr));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        App\Models\User::where('email', '=', 'admin@3l.ru')->delete();
    }
};
