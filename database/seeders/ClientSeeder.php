<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\ClientFamily;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = DB::table('clients_raw')
            ->select(['*'])
            ->get();
        foreach ($data as $d) {
            $code = $d->Family_Code;
            if($code != null && strlen($code) < 4) {
                $n = substr($code, 1);
                $code = 'F'.Str::padLeft($n, 3, '0');
            }
            $f = isset($code) ? ClientFamily::where('code', $code)->get()->first() : null;
            if ($f != null) {
                $fid = $f->id;
            } else {
                $fid = null;
                info('$fid null for fcode: '.$d->Family_Code.', calulated code: '. $code);
            }

            $cl = Client::where('client_code', $d->Client_Code)->get()->first();
            if ($cl == null) {
                Client::create([
                    'rm_id' => $d->RM_ID,
                    'client_code' => $d->Client_Code,
                    'unique_code' => $d->Unique_Code,
                    'name' => $d->Name,
                    'fresh_fund' => $d->Fresh_Fund,
                    're_invest' => $d->Re_Invest,
                    'withdrawal' => $d->Withdrawal,
                    'payout' => $d->Payout,
                    'total_aum' => $d->Total_AUM,
                    'other_funds' => $d->Other_Funds,
                    'brokerage' => $d->Brokerage,
                    'pfo_type' => $d->PFO_Type,
                    'category' => $d->Category,
                    'type' => $d->Type,
                    'fno' => $d->FNO,
                    'entry_date' => $d->Entry_Date,
                    'pan_number' => $d->PAN_Number,
                    'email' => $d->Email,
                    'phone_number' => $d->Phone_Number,
                    'whatsapp' => $d->WhatsApp,
                    'family_id' => $fid,
                ]);
            } else {
                info('Duplicate entry for client code: '.$d->Client_Code);
            }
        }
    }
}
