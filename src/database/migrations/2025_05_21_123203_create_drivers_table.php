<?php

use App\Models\DriverLicenseCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_license_category', function (Blueprint $table) {
            $table->id();
            $table->string('literal', 5);
            $table->string('name')->nullable(true)->default(null);
            $table->string('description')->nullable(true)->default(null);
            $table->string('example')->nullable(true)->default(null);
            $table->softDeletes();
            $table->timestamps();
        });


        $driverLicenseCategories = [
            'A' => [
                'name' => 'Мотоциклы',
                'description' => 'Мотоциклы с коляской или без, объём двигателя не ограничен',
                'example' => 'Мотоциклы, мотороллеры',
            ],
            'A1' => [
                'name' => 'Лёгкие мотоциклы',
                'description' => 'До 125 см³, мощность до 11 кВт',
                'example' => 'Скутеры, лёгкие мотоциклы'
            ],
            'M' => [
                'name' => 'Мопеды и скутеры',
                'description' => 'До 50 см³ или электродвигатель до 4 кВт',
                'example' => 'Мопеды, скутеры'
            ],
            'B' => [
                'name' => 'Легковые автомобили',
                'description' => 'Массой до 3,5 т, до 8 пассажиров, с прицепом до 750 кг',
                'example' => 'Легковые автомобили, внедорожники'
            ],
            'BE' => [
                'name' => 'Легковые автомобили с прицепом',
                'description' => 'Автомобили категории B с прицепом тяжелее 750 кг',
                'example' => 'Автомобиль с тяжёлым прицепом'
            ],
            'B1' => [
                'name' => 'Трициклы и квадрициклы',
                'description' => 'Не путать с квадроциклами (те относятся к A1 или спецправам)',
                'example' => 'Трициклы, квадрициклы'
            ],
            'C' => [
                'name' => 'Грузовики',
                'description' => 'Массой от 3,5 т (можно с лёгким прицепом до 750 кг)',
                'example' => 'Грузовые автомобили'
            ],
            'CE' => [
                'name' => 'Грузовики с прицепом',
                'description' => 'Грузовики категории C с прицепом тяжелее 750 кг',
                'example' => 'Фуры, автопоезда'
            ],
            'C1' => [
                'name' => 'Средние грузовики',
                'description' => 'Массой 3,5-7,5 т',
                'example' => 'Среднетоннажные грузовики'
            ],
            'C1E' => [
                'name' => 'Средние грузовики с прицепом',
                'description' => 'Грузовики категории C1 с тяжёлым прицепом (общая масса до 12 т)',
                'example' => 'Среднетоннажники с прицепом'
            ],
            'D' => [
                'name' => 'Автобусы',
                'description' => 'Более 8 пассажиров',
                'example' => 'Городские автобусы'
            ],
            'DE' => [
                'name' => 'Автобусы с прицепом',
                'description' => 'Сочленённые автобусы',
                'example' => 'Автобусы-гармошки'
            ],
            'D1' => [
                'name' => 'Микроавтобусы',
                'description' => '9-16 пассажиров',
                'example' => 'Газели, микроавтобусы'
            ],
            'D1E' => [
                'name' => 'Микроавтобусы с прицепом',
                'description' => 'Микроавтобусы категории D1 с прицепом',
                'example' => 'Микроавтобус с прицепом'
            ],
            'Tm' => [
                'name' => 'Трамваи',
                'description' => 'С 2016 года в России',
                'example' => 'Городские трамваи'
            ],
            'Tb' => [
                'name' => 'Троллейбусы',
                'description' => 'С 2016 года в России',
                'example' => 'Городские троллейбусы'
            ],
            'AS' => [
                'name' => 'Разрешение на автомобильный руль',
                'description' => 'Для категории B1 - управление с автомобильным рулём'
            ],
            'MS' => [
                'name' => 'Разрешение на мотоциклетный руль',
                'description' => 'Для квадрициклов с мотоциклетным рулём (старые права)'
            ]
        ];

        $disable_arr = ['A', 'A1', 'M', 'B1', 'D', 'DE', 'Tm', 'Tb', 'AS', 'MS'];

        foreach($driverLicenseCategories as $driverLicenseCategory => $driverLicenseCategoryArr) {
            $new = new DriverLicenseCategory;
            $new->literal = $driverLicenseCategory;
            $new->fill($driverLicenseCategoryArr);
            $new->save();
            if(in_array($new->literal, $disable_arr)){
                $new->delete();
            }
        }

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(User::class);
            $table->string('comment')->nullable()->default(null);
            $table->string('license_id')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('invite_hash')->nullable()->default(null);
            $table->string('invite_accepted_date')->nullable()->default(null);
            $table->string('active')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment() !== 'master') {
            $faker = FakerFactory::create('ru_RU'); // Russian data generator

            // Ensure user with id 3 exists
            if (User::where('id', 3)->exists()) {
                $drivers = [];

                for ($i = 0; $i < 20; $i++) {
                    $drivers[] = [
                        'name' => $faker->lastName . ' ' . $faker->firstName . ' ' . $faker->middleName,
                        'user_id' => 3,
                        'comment' => $faker->optional(0.7)->sentence,
                        'license_id' => 'АВ' . $faker->unique()->numberBetween(100000, 999999),
                        'phone' => '+7' . $faker->numerify('9########'),
                        'email' => $faker->safeEmail,
                        'invite_hash' => $faker->optional(0.6)->md5,
                        'invite_accepted_date' => $faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
                        'active' => $faker->boolean(80), // 80% chance of being active
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('drivers')->insert($drivers);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('driver_license_category');
    }
};
