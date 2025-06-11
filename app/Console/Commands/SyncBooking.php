<?php

namespace App\Console\Commands;

use App\Models\FB_Corr;
use App\Models\FB_SupplierTransport;
use App\Models\Gate;
use App\Models\GateBooking;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zjf_lll:sync-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with RS SUPPLIER_TRANSPORT_PROFORM and SUPPLIER_TRANSPORT tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $last_rs_id = GateBooking::max('rs_id');
        $last_id = GateBooking::select('id')->where('rs_id', $last_rs_id)->first()->id;
        $this->info('max rs id = '. $last_rs_id . ' / max local id = ' . $last_id);
        $rs_new = FB_SupplierTransport::where('ST_ID', '>', $last_rs_id)->get();
        $local_new = GateBooking::whereNull('rs_id')->get();

        $this->newLine();
        $this->info('sync corrs');
        $this->createNewCorrs($rs_new);

        $this->newLine();
        $this->info('sync new from rs booking');
        $bar = $this->output->createProgressBar(count($rs_new));
        foreach ($rs_new as $row) {
            $this->getNewFromRs($row);
            $bar->advance();
        }
        $bar->finish();

        $this->newLine();
        $this->info('sync new from local booking');
        $bar = $this->output->createProgressBar(count($local_new));
        foreach ($local_new as $row) {
            $this->sendNewToRs($row);
            $bar->advance();
        }
        $bar->finish();

        $this->newLine();
    }

    private function sendNewToRs($row){

    }

    private function get_local_corr_id($id){
        return Supplier::where('rs_id', '=', $id)->first()->user_id;
    }

    private function getNewFromRs($row){

        $gate_id = null;
        $gate_gbort = false;
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
                # $this->info(' created gate: ' . $gate->name . ' with id ' . $gate->id);
            }
            $gate_id = $gate->id;
            $gate_gbort = $gate->gbort;
        }

        $b_date = Carbon::parse($row->ST_ARRIVAL);
        $b_date_start = Carbon::parse($row->ST_UN_START);
        $b_date_end = Carbon::parse($row->ST_UN_END);
        $gate_bookings = new GateBooking();
        $gate_bookings->fill([
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
            'gbort' => $gate_gbort,
            'car_status_id' => null,
            'car_type_id' => 1,
            'user_id' => $this->get_local_corr_id($row->ST_CORR),
            'is_internal' => false,
            'created_at' => $b_date,
            'updated_at' => $b_date,
            'rs_id' => $row->ST_ID,
        ]);
        $gate_bookings->save();
        # $this->info(' created gate_booking with rs_id: ' . $row->ST_ID);
    }

    private function getCorrData($corr_ids){
        return DB::connection('firebird')
            ->table('CORR')
            ->select("CORR.*")
            ->addSelect(DB::raw('(SELECT FIRST 1 cp1."Info" FROM "Crm_PersonContact" as cp1 WHERE cp1.CORR_ID = CORR.CORR_ID AND cp1."ContactType" = 0) as email'))
            ->addSelect(DB::raw('(SELECT FIRST 1 cp2."Info" FROM "Crm_PersonContact" as cp2 WHERE cp2.CORR_ID = CORR.CORR_ID AND cp2."ContactType" = 1) as phone'))
            ->addSelect(DB::raw('(SELECT FIRST 1 CORR_GUID FROM L3_KS1S_CORR as one_ass_c WHERE one_ass_c.CORR_ID = CORR.CORR_ID) as one_ass_guid'))
            ->whereIn('CORR.CORR_ID', $corr_ids)
            ->get();
    }

    private function createNewCorrs($rs_new){
        $corr_ids = [];
        foreach($rs_new as $row){
            $corr_ids[$row->ST_CORR] = $row->ST_CORR;
        }
        $corr_data = $this->getCorrData($corr_ids);

        $bar = $this->output->createProgressBar(count($corr_data));

        foreach($corr_data as $corr){
            $supplier = Supplier::where('rs_id', '=', $corr->CORR_ID)->first();
            if (!$supplier) {
                $pass = Hash::make('password_passw0rd_' . $corr->CORR_ID . '_passw0rd_password');

                $email = $corr->EMAIL;
                if (!$corr->EMAIL || User::where('email', '=', $corr->EMAIL)->first()) {
                    $email = $corr->EMAIL . '|' . $corr->CORR_ID;
                }

                $user = new User();
                $user->password = $pass;
                $user->email = $email;
                $user->name = $corr->CORR_NAME;
                $user->save();

                $user->assignRole('supplier');


                $supplier = new Supplier();
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

                # $this->info(' created supplier: ' . $supplier->name . ' with id ' . $user->id);
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
