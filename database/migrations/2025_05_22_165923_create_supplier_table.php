<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as FakerFactory;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignId('user_id')->constrained();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip')->nullable();
            $table->string('inn')->nullable();
            $table->integer('rs_id')->nullable();
            $table->string('one_ass_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });


//        if (app()->environment() !== 'master') {
//            $path = dirname(__FILE__) . '/../../.ht.pass';
//
//            $pass_arr = [];
//            if (file_exists($path)) {
//                $pass_arr = @unserialize(file_get_contents($path));
//                if (!is_array($pass_arr)) {
//                    $pass_arr = [];
//                }
//            }
//
//            $faker = FakerFactory::create('ru_RU');
//            $output = new ConsoleOutput();
//
//            for ($i = 1; $i <= 5; $i++) {
//                $user_row_ = 'supplier_' . $i;
//                if (!isset($pass_arr[$user_row_])) {
//                    $pass_ = Str::random(32);
//                    $output->writeln('<info>' . $user_row_ . ' pass: ' . $pass_ . '</info>');
//                    $pass = Hash::make($pass_);
//                    $pass_arr[$user_row_] = $pass_;
//                } else {
//                    $pass = Hash::make($pass_arr[$user_row_]);
//                }
//
//                $user = new App\Models\User();
//                $user->password = $pass;
//                $user->email = $faker->safeEmail;
//                $user->name = $user_row_;
//                $user->save();
//
//                $user->assignRole('supplier');
//
//                $supplier = new App\Models\Supplier();
//                $supplier->name = $faker->company();
//                $supplier->user_id = $user->id;
//                $supplier->phone = '+7' . $faker->numerify('9########');
//                $supplier->email = $user->email;
//                $supplier->address = $faker->address();
//                $supplier->city = $faker->city();
//                $supplier->state = $faker->city();
//                $supplier->country = $faker->country();
//                $supplier->zip = $faker->postcode();
//                $supplier->inn = $faker->numerify('9########');
//                $supplier->rs_id = $faker->numberBetween(100000, 999999);
//                $supplier->one_ass_id = $faker->numberBetween(100000, 999999);
//                $supplier->save();
//
//
//                $count = rand(3, 20);
//                $drivers = [];
//                for($ii=0;$ii<=$count;$ii++){
//                    $drivers[] = [
//                        'name' => $faker->lastName . ' ' . $faker->firstName . ' ' . $faker->middleName,
//                        'user_id' => $user->id,
//                        'comment' => $faker->optional(0.7)->sentence,
//                        'license_id' => 'АВ' . $faker->unique()->numberBetween(100000, 999999),
//                        'phone' => '+7' . $faker->numerify('9########'),
//                        'email' => $faker->safeEmail,
//                        'invite_hash' => $faker->optional(0.6)->md5,
//                        'invite_accepted_date' => $faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
//                        'active' => 1,
//                        'created_at' => now(),
//                        'updated_at' => now(),
//                    ];
//                }
//                DB::table('drivers')->insert($drivers);
//
//
//                $count = rand(3, 20);
//                $arr = [];
//                for ($ii = 0; $ii < $count; $ii++) {
//                    $arr[] = [
//                        'name' => $faker->lastName . ' ' . $faker->firstName . ' ' . $faker->middleName,
//                        'user_id' => $user->id,
//                        'comment' => $faker->optional(0.7)->sentence,
//                        'phone' => '+7' . $faker->numerify('9########'),
//                        'email' => $faker->safeEmail,
//                        'created_at' => now(),
//                        'updated_at' => now(),
//                    ];
//                }
//
//                DB::table('expeditors')->insert($arr);
//            }
//
//            file_put_contents($path, serialize($pass_arr));
//        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
