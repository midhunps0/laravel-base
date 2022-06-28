<?php

namespace Database\Seeders;

use App\Models\Script;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('scriptmaster.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $data = DB::table('scripmaster')
            ->select(['*'])
            ->get();
        foreach ($data as $script) {
            Script::create([
                'isin_code' => $script->ISIN_Code,
                'symbol' =>  $script->Symbol,
                'company_name' => $script->Company_Name,
                'industry' => $script->Industry,
                'series' => $script->Series,
                'fno' => $script->FNO,
                'nifty' => $script->Nifty,
                'nse_code' => $script->NSE_code,
                'bse_code' => $script->BSE_CODE,
                'yahoo_code' => $script->Yahoo_code,
                'doc' => $script->Doc,
                'bbg_ticker' => $script->BBG_TICKER,
                'bse_security_id' => $script->BSE_Security_ID,
                'capitaline_code' => $script->Capitaline_Code,
                'mvg_sector' => $script->MVG_Sector,
                'agio_indutry' => $script->Agio_Indutry,
                'remarks' => $script->Remarks,
            ]);
        }

        Schema::dropIfExists('scripmaster');
    }
}
