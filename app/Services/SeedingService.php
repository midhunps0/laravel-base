<?php
namespace \App\Services;

use App\Models\ClientFamily;
use Illuminate\Support\Facades\DB;

class SeedingService
{
    public function clientSeed()
    {
        $data = DB::table('clients_raw')
            ->select(['*'])
            ->get();
        foreach ($data as $d) {
            $fid = ClientFamily::where('code', $d['Family_Code'])->get()->first()->id;
            Client::create([
                'rm_id' => $d['RM_ID'],
                'client_code' => $d['Client_Code'],
                'unique_code' => $d['Unique_Code'],
                'name' => $d['Name'],
                'fresh_fund' => $d['Fresh_Fund'],
                're_invest' => $d['Re_Invest'],
                'withdrawal' => $d['Withdrawal'],
                'payout' => $d['Payout'],
                'total_aum' => $d['Total_AUM'],
                'other_funds' => $d['Other_Funds'],
                'brokerage' => $d['Brokerage'],
                'pfo_type' => $d['PFO_Type'],
                'category' => $d['Category'],
                'type' => $d['Type'],
                'fno' => $d['FNO'],
                'entry_date' => $d['Entry_Date'],
                'pan_number' => $d['PAN_Number'],
                'email' => $d['Email'],
                'phone_number' => $d['Phone_Number'],
                'whatsapp' => $d['WhatsApp'],
                'family_id' => $fid,
            ]);
        }
    }
}
?>