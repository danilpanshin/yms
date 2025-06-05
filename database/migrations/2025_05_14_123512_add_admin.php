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
        $path = dirname(__FILE__) . '/../../.ht.pass';

        $pass_arr = [];
        if(file_exists($path)){
            $pass_arr = @unserialize(file_get_contents($path));
            if(!is_array($pass_arr)){
                $pass_arr = [];
            }
        }

        if(!isset($pass_arr['lll_admin'])){
            $pass = Str::random(32);
            $output = new ConsoleOutput();
            $output->writeln('<info>lll_admin pass: ' . $pass . '</info>');
            $pass = Hash::make($pass);
            $pass_arr['lll_admin'] = $pass;
        } else {
            $pass = $pass_arr['admin'];
        }

        $user = new App\Models\User();
        $user->password = $pass;
        $user->email = 'admin@3l.ru';
        $user->name = 'lll_admin';
        $user->save();

        $user->assignRole('admin');

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
