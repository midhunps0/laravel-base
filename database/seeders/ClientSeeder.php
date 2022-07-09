<?php

namespace Database\Seeders;

use App\Helpers\GeneralHelper;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\ClientFamily;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $path = database_path('clients.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

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
                    'total_aum' => abs($d->Total_AUM),
                    'other_funds' => $d->Other_Funds,
                    'brokerage' => $d->Brokerage,
                    'realised_pnl' => $d->Total_AUM == 0 ? 0 : $d->Total_AUM * GeneralHelper::randomPercentage(-5, 30) / 100,
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

        Schema::dropIfExists('clients_raw');
    }
}
