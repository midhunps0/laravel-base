<?php
namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\ClientScript;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;

class ClientScriptService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->selects = [
            'cs.id as csid',
            'c.id as client_id',
            's.id as script_id',
            'c.client_code as client_code',
            's.symbol as symbol',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as avg_buy_price',
            DB::raw("cs.dp_qty * cs.buy_avg_price AS avg_buy_value"),
            'c.rm_id as rm_id'
        ];
        $this->query = ClientScript::query()->from('clients_scripts as cs')
            ->join('clients as c', 'cs.client_id', '=', 'c.id')
            ->join('scripts as s', 'cs.script_id', '=', 's.id')
            ->userAccessControlled();
    }

    private function getFilterParams($query, array $filters): array
    {
        return [];
    }
}
?>