<?php

use App\Models\Acceptance;
use App\Models\CarType;
use App\Models\Driver;
use App\Models\Expeditor;
use App\Models\Gate;
use App\Models\GateBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gate_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->constrained();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('pallets_count');
            $table->float('weight', 6)->default(0);
            $table->string('purpose');
            $table->string('car_number');
            $table->foreignId('acceptances_id')->constrained();
            $table->boolean('gbort')->default(false);
            $table->foreignId('car_type_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('expeditor_id')->constrained();
            $table->boolean('is_internal')->default(false);
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['gate_id', 'booking_date']);
            $table->index(['start_time']);
            $table->index(['end_time']);
            $table->index(['is_internal']);
            $table->index(['user_id']);
        });

        if (app()->environment() !== 'master') {
            $faker = FakerFactory::create('ru_RU');

            if (Gate::exists() && User::whereIn('id', [2,3,4,5])->exists()) {
                $bookings = [];
                $gateIds = Gate::pluck('id')->toArray();
                $carTypeIds = CarType::pluck('id')->toArray();
                $driverIds = Driver::pluck('id')->toArray();
                $expeditorIds = Expeditor::pluck('id')->toArray();
                $acceptanceIds = Acceptance::pluck('id')->toArray();
                $purposes = [
                    'Загрузка товара',
                    'Выгрузка товара',
                    'Приемка поставки',
                    'Отгрузка клиенту',
                    'Возврат товара',
                    'Инвентаризация',
                    'Межскладское перемещение'
                ];

                for ($i = 0; $i < 2000; $i++) {
                    // Генерируем случайную дату в ближайшие 30 дней
                    $bookingDate_c = Carbon::now()->addDays(rand(-300, 13));
                    $bookingDate = $bookingDate_c->format('Y-m-d');

                    $palletsCount = rand(1, 100);

                    // Генерируем случайное время с шагом 15 минут
                    $hour = rand(8, 19); // Рабочие часы с 8 до 20
                    $minute = rand(0, 3) * 15; // 0, 15, 30 или 45 минут

                    $startTime_c = Carbon::now();
                    $startTime_c->setHour($hour);
                    $startTime_c->setMinute($minute);
                    $startTime_c->setSecond(0);
                    $startTime = $startTime_c->format('H:i:s');

                    $endTimeUnix = $startTime_c->getTimestamp() + (ceil($palletsCount / 33) * 60 * 60) + (60 * 60 * 3);
                    $endTime = Carbon::createFromTimestamp($endTimeUnix)->format('H:i:s');


                    DB::table('gate_bookings')->insert([
                        'driver_id' => $faker->randomElement($driverIds),
                        'gate_id' => $faker->randomElement($gateIds),
                        'expeditor_id' => $faker->randomElement($expeditorIds),
                        'booking_date' => $bookingDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'pallets_count' => $palletsCount,
                        'weight' => $faker->randomFloat(0.1, 1230),
                        'purpose' => $faker->randomElement($purposes),
                        'car_number' => strtoupper($faker->randomLetter().$faker->randomLetter()) . $faker->unique()->numberBetween(100000, 999999) . strtoupper($faker->randomLetter()),
                        'acceptances_id' => $faker->randomElement($acceptanceIds),
                        'gbort' => $faker->randomElement([true, false]),
                        'status' => $faker->randomElement(['Отгружено', 'На территории', 'Новый', 'Отменен']),
                        'car_type_id' => $faker->randomElement($carTypeIds),
                        'user_id' => 3,
                        'is_internal' => $faker->boolean(1),
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_bookings');
    }
};
