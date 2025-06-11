<?php

use App\Models\Acceptance;
use App\Models\CarStatus;
use App\Models\CarType;
use App\Models\Driver;
use App\Models\Expeditor;
use App\Models\FB_Corr;
use App\Models\FB_SupplierTransport;
use App\Models\Gate;
use App\Models\GateBooking;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use function App\Models\GateBooking;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $corr_ids = [];
        $old_data = FB_SupplierTransport::orderBy('ST_ID', 'asc')->get();
        foreach($old_data as $row){
            $corr_ids[$row->ST_CORR] = $row->ST_CORR;
        }

        # $corrs = FB_Corr::whereIn('CORR_ID', $corr_ids)->get();

        $import_from_rs = true;

        if($import_from_rs) {

            $corrs = DB::connection('firebird')
                ->table('CORR')
                ->select("CORR.*")
                ->addSelect(DB::raw('(SELECT FIRST 1 cp1."Info" FROM "Crm_PersonContact" as cp1 WHERE cp1.CORR_ID = CORR.CORR_ID AND cp1."ContactType" = 0) as email'))
                ->addSelect(DB::raw('(SELECT FIRST 1 cp2."Info" FROM "Crm_PersonContact" as cp2 WHERE cp2.CORR_ID = CORR.CORR_ID AND cp2."ContactType" = 1) as phone'))
                ->addSelect(DB::raw('(SELECT FIRST 1 CORR_GUID FROM L3_KS1S_CORR as one_ass_c WHERE one_ass_c.CORR_ID = CORR.CORR_ID) as one_ass_guid'))
                ->whereIn('CORR.CORR_ID', $corr_ids)
                ->get();

            $corr_user_ids = [];

            foreach ($corrs as $corr) {
                $user = null;
                $supplier = Supplier::where('rs_id', '=', $corr->CORR_ID)->first();
                if ($supplier) {
                    $user = User::find($supplier->user_id);
                }
                if (!$user) {
                    $pass = Hash::make('password_passw0rd_' . $corr->CORR_ID . '_passw0rd_password');

                    $email = $corr->EMAIL;
                    if (!$corr->EMAIL || User::where('email', '=', $corr->EMAIL)->first()) {
                        $email = $corr->EMAIL . '|' . $corr->CORR_ID;
                    }

                    $user = new App\Models\User();
                    $user->password = $pass;
                    $user->email = $email;
                    $user->name = $corr->CORR_NAME;
                    $user->save();

                    $user->assignRole('supplier');


                    $supplier = new App\Models\Supplier();
                    $supplier->name = $corr->CORR_NAME;
                    $supplier->user_id = $user->id;
                    $supplier->phone = $corr->PHONE ?? '';
                    $supplier->email = $email;
                    $supplier->address = '';
                    $supplier->city = '';
                    $supplier->state = '';
                    $supplier->country = '';
                    $supplier->zip = '';
                    $supplier->inn = $corr->CORR_INN;
                    $supplier->rs_id = $corr->CORR_ID;
                    $supplier->one_ass_id = $corr->ONE_ASS_GUID;
                    $supplier->save();
                }

                $corr_user_ids[$corr->CORR_ID] = $user->id;
            }

            foreach ($old_data as $row) {
                $gate_id = null;
                if ($row->UNLOADING_GATE !== '0') {
                    $gate = Gate::where('number', '=', (int)$row->UNLOADING_GATE)->first();
                    if (!$gate) {
                        $gate = new Gate();
                        $gate->number = (int)$row->UNLOADING_GATE;
                        $gate->wh_number = 20;
                        $gate->name = 'Ворота 20/' . (int)$row->UNLOADING_GATE;
                        $gate->comment = 'from rs';
                        $gate->is_active = 1;
                        $gate->gbort = false;
                        $gate->save();
                    }
                    $gate_id = $gate->id;
                }

                $b_date = Carbon::parse($row->ST_ARRIVAL);
                $b_date_start = Carbon::parse($row->ST_UN_START);
                $b_date_end = Carbon::parse($row->ST_UN_END);
                $gate_booking = new GateBooking();
                $gate_booking->fill([
                    'driver_id' => null,
                    'gate_id' => $gate_id,
                    'expeditor_id' => null,
                    'booking_date' => $b_date,
                    'start_time' => $b_date_start,
                    'end_time' => $b_date_end,
                    'pallets_count' => $row->ST_NUM_PLACES_ACC,
                    'weight' => null,
                    'purpose' => '',
                    'car_number' => $row->ST_TRANS_NO,
                    'acceptances_id' => 1,
                    'gbort' => false,
                    'car_status_id' => null,
                    'car_type_id' => 1,
                    'user_id' => $corr_user_ids[$row->ST_CORR],
                    'is_internal' => false,
                    'created_at' => $b_date,
                    'updated_at' => $b_date,
                    'rs_id' => $row->ST_ID,
                ]);
                $gate_booking->save();
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
