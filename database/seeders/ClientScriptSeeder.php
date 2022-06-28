<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Script;
use App\Models\ClientScript;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::dropIfExists('portfolio');

        $path = database_path('portfolio.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $data = DB::table('portfolio')
            ->select(['*'])
            ->get();

        foreach ($data as $item) {
            $client = Client::where('client_code', $item->client_code)->get()->first();
            $script = Script::where('isin_code', $item->isin_no)->get()->first();
            if ($client == null || $script == null) {
                $param = $client == null ? 'client' : 'script';
                $code = $client == null ? 'client_code' : 'isin_no';
                info('Unmatched '.$param.': '.$item->$code);
                continue;
            }
            $clientId = $client->id;
            $scriptId = $script->id;
            ClientScript::create([
                'client_id' => $clientId,
                'script_id' => $scriptId,
                'entry_date' => $item->entry_date,
                'dp_qty' => $item->dp_qty,
                'available_qty' => $item->available_qty,
                'buy_avg_price' => $item->buy_avg_price
            ]);
        }

        Schema::dropIfExists('portfolio');
    }
}
