<?php
namespace App\Services;

use App\Models\Role;
use App\Models\Client;
use App\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use App\Services\IsModelViewConnector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AggregatesService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $adminSelects = [];
    private $rmSelects = [];
    private $adminIdKey = 'rmid';
    private $rmIdKey = 'cid';


    public function __construct()
    {
        $this->adminSelects = [
            'u.id as rmid',
            'u.name as rm',
            'c.category as category',
            DB::raw('COUNT(DISTINCT c.id) as count'),
            DB::raw('SUM(c.total_aum) as aum'),
            DB::raw('SUM(cs.dp_qty * cs.buy_avg_price) as buy_value'),
            DB::raw('SUM(cs.dp_qty * s.cmp) as current_value'),
            DB::raw('SUM((cs.dp_qty * s.cmp) - (cs.dp_qty * cs.buy_avg_price)) as pnl'),
            DB::raw('SUM((cs.dp_qty * s.cmp) - (cs.dp_qty * cs.buy_avg_price)) / SUM(cs.dp_qty * cs.buy_avg_price) * 100 as pnl_pc'),
            DB::raw('SUM(c.realised_pnl) as realised_pnl'),
            // total_aum - allocated_aum + liquidbees
            DB::raw('SUM(c.total_aum) - SUM(cs.dp_qty * cs.buy_avg_price) + IFNULL(SUM(lb.liquidbees), 0) as cash'),
            DB::raw('(SUM(c.total_aum) - SUM(cs.dp_qty * cs.buy_avg_price) + IFNULL(SUM(lb.liquidbees), 0)) / SUM(c.total_aum) * 100 as cash_pc'),
            DB::raw('SUM(c.realised_pnl) + SUM(cs.dp_qty * s.cmp) - SUM(c.total_aum) as returns'),
            DB::raw('(SUM(c.realised_pnl) + SUM(cs.dp_qty * s.cmp) - SUM(c.total_aum)) / SUM(c.total_aum) * 100 as returns_pc'),
        ];

        $this->searchesMap = [
            'category' => 'c.category'
        ];

        $this->rmSelects = [
            'c.category as category',
            DB::raw('COUNT(DISTINCT c.id) as count'),
            DB::raw('SUM(c.total_aum) as aum'),
            DB::raw('SUM(cs.dp_qty * cs.buy_avg_price) as buy_value'),
            DB::raw('SUM(cs.dp_qty * s.cmp) as current_value'),
            DB::raw('SUM((cs.dp_qty * s.cmp) - (cs.dp_qty * cs.buy_avg_price)) as pnl'),
            DB::raw('SUM((cs.dp_qty * s.cmp) - (cs.dp_qty * cs.buy_avg_price)) / SUM(cs.dp_qty * cs.buy_avg_price) * 100 as pnl_pc'),
            DB::raw('SUM(c.realised_pnl) as realised_pnl'),
            // total_aum - allocated_aum + liquidbees
            DB::raw('SUM(c.total_aum) - SUM(cs.dp_qty * cs.buy_avg_price) + IFNULL(SUM(lb.liquidbees), 0) as cash'),
            DB::raw('(SUM(c.total_aum) - SUM(cs.dp_qty * cs.buy_avg_price) + IFNULL(SUM(lb.liquidbees), 0)) / SUM(c.total_aum) * 100 as cash_pc'),
            DB::raw('SUM(c.realised_pnl) + SUM(cs.dp_qty * s.cmp) - SUM(c.total_aum) as returns'),
            DB::raw('(SUM(c.realised_pnl) + SUM(cs.dp_qty * s.cmp) - SUM(c.total_aum)) / SUM(c.total_aum) * 100 as returns_pc'),
        ];
    }

    public function adminIndex(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds,
        string $resultsName = 'results'
    ): array
    {
        $queryData = $this->getAdminQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );
// dd($queryData['query']->select($this->adminSelects)->groupBy('rmid')->toSql());
        DB::statement("SET SQL_MODE=''");

        // $results = $queryData['query']->paginate(
        //     $itemsCount,
        //     $this->selects,
        //     'page',
        //     $page
        // );

        $results = $queryData['query']->select($this->adminSelects)
            // ->whereRaw('c.rm_id IN (1,7,8,9)')
            ->groupBy('rmid')
            ->get();

        DB::statement("SET SQL_MODE='only_full_group_by'");

        $data = $results->toArray();
// dd(json_encode($data));
        // $paginator = $this->getPaginatorArray($results);
// dd(json_encode($data));
        return [
            $resultsName => $results,
            // 'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'results_json' => json_encode($data),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'total_results' => count($data),
            'current_page' => 1,
            // 'paginator' => json_encode($paginator),
            'paginator' => json_encode([]),
            'route' => Request::route()->getName()
        ];
    }

    public function getAdminQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch = [],
        string $selectedIds = ''
    ): array {
        $query = $this->getAdminQuery();
        $filterData = $this->getFilterParams($query, $filters);
        $searchParams = $this->getSearchParams($query, $searches);
        $sortParams = $this->getSortParams($query, $sorts);
        $advParams = $this->getAdvParams($query, $advSearch);

        $this->extraConditions($query);

        if (isset($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            // $this->query->whereIn('c.id', $ids);
            $this->querySelectedIds($query, $this->selIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData,
            'advparams' => $advParams
        ];
    }

    private function getAdminQuery()
    {
        $lbs = AppHelper::getLiquidbees();

        $lqbs = DB::table('clients_scripts as lbqs')
            ->join('clients as cl', 'lbqs.client_id', '=', 'cl.id')
            ->join('users as us', 'us.id', '=', 'cl.rm_id')
            ->select(
                'us.id as lbqs_rm_id',
                'lbqs.client_id as lbqs_client_id',
                'lbqs.script_id as lbqs_script_id',
                DB::raw('IFNULL('.DB::raw('SUM(lbqs.dp_qty * lbqs.buy_avg_price)').', 0) as liquidbees')
            )
            ->whereIn('script_id', $lbs)
            ->groupBy('us.id');

        $role_rm = Role::where('name', 'Dealer')->get()->first();

        $rmIds = $role_rm->users()->pluck('id')->toArray();

        $query = DB::table('users as u')
            ->leftJoin('clients as c', 'c.rm_id', '=', 'u.id')
            ->leftJoin('clients_scripts as cs', 'cs.client_id', '=', 'c.id')
            ->leftJoin('scripts as s', 'cs.script_id', '=', 's.id')
            ->leftJoinSub($lqbs, 'lb', 'lb.lbqs_rm_id', '=', 'u.id')
            ->whereRaw('c.rm_id IN ('. implode(',', $rmIds) .')');

        return $query;

    }

    public function adminSelectIds(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
    ): array {
        $queryData = $this->getAdminQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch
        );

        DB::statement("SET SQL_MODE=''");

        $results = $queryData['query']->select($this->selects)->get()->pluck($this->adminIdKey)->unique()->toArray();
        DB::statement("SET SQL_MODE='only_full_group_by'");
        return $results;
    }

    /********************* RM ***********************/

    public function rmIndex(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds,
        string $resultsName = 'results'
    ): array
    {
        $queryData = $this->getRmQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );
// dd($queryData['query']->select($this->adminSelects)->groupBy('rmid')->toSql());
        DB::statement("SET SQL_MODE=''");

        // $results = $queryData['query']->paginate(
        //     $itemsCount,
        //     $this->selects,
        //     'page',
        //     $page
        // );

        $results = $queryData['query']->select($this->rmSelects)
            // ->whereRaw('c.rm_id IN (1,7,8,9)')
            ->groupBy('category')
            ->get();

        DB::statement("SET SQL_MODE='only_full_group_by'");

        $data = $results->toArray();
// dd(json_encode($data));
        // $paginator = $this->getPaginatorArray($results);
// dd(json_encode($data));
        return [
            $resultsName => $results,
            // 'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'results_json' => json_encode($data),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'total_results' => count($data),
            'current_page' => 1,
            // 'paginator' => json_encode($paginator),
            'paginator' => json_encode([]),
            'route' => Request::route()->getName()
        ];
    }

    public function getRmQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch = [],
        string $selectedIds = ''
    ): array {
        $query = $this->getRmQuery();
        $filterData = $this->getFilterParams($query, $filters);
        $searchParams = $this->getSearchParams($query, $searches);
        $sortParams = $this->getSortParams($query, $sorts);
        $advParams = $this->getAdvParams($query, $advSearch);

        $this->extraConditions($query);

        if (isset($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            // $this->query->whereIn('c.id', $ids);
            $this->querySelectedIds($query, $this->selIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData,
            'advparams' => $advParams
        ];
    }

    private function getRmQuery()
    {
        $lbs = AppHelper::getLiquidbees();

        $lqbs = DB::table('clients_scripts as lbqs')
            // ->join('clients as cl', 'lbqs.client_id', '=', 'cl.id')
            // ->join('users as us', 'us.id', '=', 'cl.rm_id')
            ->select(
                // 'cl.id as lbqs_client_id',
                'lbqs.client_id as lbqs_client_id',
                'lbqs.script_id as lbqs_script_id',
                DB::raw('IFNULL('.DB::raw('SUM(lbqs.dp_qty * lbqs.buy_avg_price)').', 0) as liquidbees')
            )
            ->whereIn('script_id', $lbs)
            ->groupBy('lbqs.client_id');

        // $categories = DB::table('clients as c')
        //         ->selectRaw('DISTINCT c.category as category');

        $query = Client::from('clients as c')->userAccessControlled()
                ->leftJoin('clients_scripts as cs', 'cs.client_id', '=', 'c.id')
                ->leftJoin('scripts as s', 'cs.script_id', '=', 's.id')
                ->leftJoinSub($lqbs, 'lb', 'lb.lbqs_client_id', '=', 'c.id');

        return $query;

    }

    public function rmSelectIds(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
    ): array {
        $queryData = $this->getAdminQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch
        );

        DB::statement("SET SQL_MODE=''");

        $results = $queryData['query']->select($this->selects)->get()->pluck($this->rmIdKey)->unique()->toArray();
        DB::statement("SET SQL_MODE='only_full_group_by'");
        return $results;
    }

    protected function getRelationQuery(int $id = null)
    {
        return null;
    }

    function accessCheck(Model $item): bool
    {
        return true;
    }
}
?>
