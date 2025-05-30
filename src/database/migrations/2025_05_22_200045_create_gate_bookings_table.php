<?php

use App\Models\Acceptance;
use App\Models\CarStatus;
use App\Models\CarType;
use App\Models\Driver;
use App\Models\Expeditor;
use App\Models\Gate;
use App\Models\GateBooking;
use App\Models\Supplier;
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
            $table->string('purpose')->nullable();
            $table->string('car_number');
            $table->foreignId('acceptances_id')->nullable()->constrained();
            $table->boolean('gbort')->default(false);
            $table->foreignId('car_type_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('expeditor_id')->nullable()->constrained();
            $table->boolean('is_internal')->nullable();
            $table->foreignId('car_status_id')->nullable()->constrained();
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

            $gateIds = Gate::pluck('id')->toArray();
            $carTypeIds = CarType::pluck('id')->toArray();
            $driverIds = Driver::pluck('id')->toArray();
            $expeditorIds = Expeditor::pluck('id')->toArray();
            $supplierIds = Supplier::pluck('id')->toArray();
            $acceptanceIds = Acceptance::pluck('id')->toArray();
            $statuses_past = CarStatus::where('id', '>', 40)->pluck('id')->toArray();
            $statuses_future = CarStatus::where('id', '<=', 40)->pluck('id')->toArray();
            $purposes = [
                'Загрузка товара',
                'Выгрузка товара',
                'Приемка поставки',
                'Отгрузка клиенту',
                'Возврат товара',
                'Инвентаризация',
                'Межскладское перемещение'
            ];


            for ($i = 0; $i < 20000; $i++) {
                // Генерируем случайную дату в ближайшие 30 дней
                $bookingDate_c = Carbon::now()->addDays(rand(-300, 60));
                if ($bookingDate_c->isPast()) {
                    $status = $faker->randomElement($statuses_past);
                } else {
                    $status = $faker->randomElement($statuses_future);
                }


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
                    'car_number' => $this->generateRussianLicensePlate(),
                    'acceptances_id' => $faker->randomElement($acceptanceIds),
                    'gbort' => $faker->randomElement([true, false]),
                    'car_status_id' => $status,
                    'car_type_id' => $faker->randomElement($carTypeIds),
                    'user_id' => $faker->randomElement($supplierIds),
                    'is_internal' => $faker->boolean(1),
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);
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

    function generateRussianLicensePlate(): string
    {
        // Буквы, используемые в номерах (кроме похожих на цифры)
        $letters = [
            'А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'
        ];

        // Регионы (коды от 01 до 999)
        $regions = [
            // Популярные регионы
            '01', '02', '03', '04', '05', '07', '08', '09', '10',
            '11', '12', '13', '14', '15', '16', '17', '18', '19',
            // Другие регионы (можно добавить все)
            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
            '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
            '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
            '60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '70', '71', '72', '73', '74', '75', '76', '77', '78', '79',
            '80', '81', '82', '83', '84', '85', '86', '87', '88', '89',
            '90', '91', '92', '93', '94', '95', '96', '97', '98', '99',
            // Трехзначные коды
            '102', '113', '116', '121', '123', '124', '125', '134', '136', '138',
            '142', '147', '150', '152', '154', '159', '161', '163', '164', '173',
            '174', '177', '178', '186', '190', '196', '197', '199', '277', '299',
            '716', '725', '750', '777', '790', '797', '799'
        ];

        // Генерация номера
        $number = '';

        // 1 буква
        $number .= $letters[array_rand($letters)];

        // 3 цифры
        $number .= str_pad((string)mt_rand(0, 999), 3, '0', STR_PAD_LEFT);

        // 2 буквы
        $number .= $letters[array_rand($letters)];
        $number .= $letters[array_rand($letters)];

        // Регион
        $region = $regions[array_rand($regions)];

        return $number . '' . $region;
    }
};
