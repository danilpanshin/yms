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
use Illuminate\Support\Facades\Auth;
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
        $last_synced = DB::table('last_synced')->find(1);

        $rs_updated = FB_SupplierTransport::where('UPDATED_AT', '>', $last_synced->last_updated_at_supplier_transport)->get();
        $rs_new = FB_SupplierTransport::where('ST_ID', '>', $last_rs_id)->get();
        $local_new = GateBooking::whereNull('rs_id')->get();


        # sync corrs
        $this->newLine();
        $this->info('sync corrs');
        $this->createNewCorrs($rs_new);

        $this->newLine();
        $this->info('sync corrs from updated');
        $this->createNewCorrs($rs_updated);


        # sync new from rs booking
        $this->newLine();
        $this->info('sync new from rs booking');
        $bar = $this->output->createProgressBar(count($rs_new));
        foreach ($rs_new as $row) {
            $this->getNewFromRs($row);
            $bar->advance();
        }
        $bar->finish();


        # sync new from local booking
        $this->newLine();
        $this->info('sync new from local booking');
        $bar = $this->output->createProgressBar(count($local_new));
        foreach ($local_new as $row) {
            $this->sendNewToRs($row);
            $bar->advance();
        }
        $bar->finish();


        # sync changes from rs
        $this->newLine();
        $this->info('sync changes from rs');
        $bar = $this->output->createProgressBar(count($rs_updated));
        foreach ($rs_updated as $row) {
            $this->getChangesFromRs($row);
            $bar->advance();
        }
        $bar->finish();
        DB::table('last_synced')->update(['last_updated_at_supplier_transport' => now()]);

        $this->newLine();
    }

    private function getChangesFromRs($row){

        extract($this->prepareArr($row));

        $old = GateBooking::withTrashed()->where('rs_id', '=', $row->ST_ID)->first();
        if($old){
            $old->forceFill([
                'gate_id' => $gate_id,
                'booking_date' => $b_date,
                'start_time' => $b_date_start,
                'end_time' => $b_date_end,
                'pallets_count' => $row->ST_NUM_PLACES_ACC,
                'car_number' => $row->ST_TRANS_NO,
                'gbort' => $gate_gbort,
                'user_id' => $this->get_local_corr_id($row->ST_CORR),
                'created_at' => $b_date,
                'updated_at' => $b_date,
                'rs_id' => $row->ST_ID,
                'car_status_id' => $arrival_status,
            ]);
            $old->save();
            if($old->wasChanged()){
                activity('sync')
                    ->event('sync booking changes')
                    ->causedBy(Auth::user())
                    ->withProperties($old->getChanges())
                    ->log('send sms');
                $old->save();
            }
        }
    }

    private function sendNewToRs($row){


        $gate = Gate::where('id', '=', $row->gate_id)->first();
        $supplier = Supplier::where('id', '=', $row->user_id)->first();

        try {
            $new = new FB_SupplierTransport;
            $new->ST_STATUS = 2;
            $new->ST_CORR = $supplier->rs_id;
            $new->ST_ARRIVAL = $row->booking_date->format('Y-m-d') . ' ' . $row->start_time->format('H:i:s');
            $new->ST_UN_START = null;
            $new->ST_UN_END = null;
            $new->ST_EXECUTOR = null;
            $new->ST_START = $row->booking_date->format('Y-m-d') . ' ' . $row->start_time->format('H:i:s');
            $new->ST_END = null;
            $new->ST_MOI_ID = null;
            $new->ST_TRANS_NO = $row->car_number;
            $new->ST_ATC_ID = 1;
            $new->ST_NUM_PLACES = $row->pallets_count;
            $new->ST_DEPARTED = null;
            $new->ST_NUM_PLACES_ACC = $row->pallets_count;
            $new->UNLOADING_GATE = $gate->number;
            $new->ST_NPA_CAUSE = null;
            $new->ST_KOL_EXEC = 0;
            $new->save();
        } catch (\PDOException $th) {
            dump($th->getMessage());
            $added = FB_SupplierTransport::where('ST_STATUS', '=', 2)
                ->where('ST_CORR', '=', $supplier->rs_id)
                ->where('ST_START', '=', $row->booking_date->format('Y-m-d') . ' ' . $row->start_time->format('H:i:s'))
                ->where('ST_TRANS_NO', '=', $row->car_number)
                ->where('ST_NUM_PLACES', '=', $row->pallets_count)
                ->where('UNLOADING_GATE', '=', $gate->number)
                ->first();
            $row->rs_id = $added->ST_ID;
            $row->save();
        }
    }

    private function get_local_corr_id($id){
        $res = Supplier::where('rs_id', '=', $id)->first();
        return $res->id;
    }

    private function prepareArr($row): array
    {
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

        $arrival_status = 10;
        if($row->ST_STATUS == 2) {
            if ($row->ST_UN_START) {
                $arrival_status = 30;
            }
            if ($row->ST_UN_END) {
                $arrival_status = 40;
            }
        } else if ($row->ST_STATUS == 0) {
            $arrival_status = 30;
        } else {
            $arrival_status = 50;
        }

        return [
            'b_date' => $b_date,
            'b_date_start' => $b_date_start,
            'b_date_end' => $b_date_end,
            'gate_id' => $gate_id,
            'gate_gbort' => $gate_gbort,
            'arrival_status' => $arrival_status,
        ];
    }

    private function getNewFromRs($row){

        extract($this->prepareArr($row));

        GateBooking::insert([
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
            'car_status_id' => $arrival_status,
            'car_type_id' => 1,
            'user_id' => $this->get_local_corr_id($row->ST_CORR),
            'is_internal' => false,
            'created_at' => $b_date,
            'updated_at' => $b_date,
            'rs_id' => $row->ST_ID,
        ]);

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
