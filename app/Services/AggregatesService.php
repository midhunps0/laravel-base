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

    public function __construct()
    {
        $this->adminSelects = [
            'u.id as rmid',
            'c.total_aum',
            DB::raw('SUM(c.total_aum)')
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
    dd($data);
        $paginator = $this->getPaginatorArray($results);

        return [
            $resultsName => $results,
            'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'total_results' => $data['total'],
            'current_page' => $data['current_page'],
            'paginator' => json_encode($paginator),
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
        $role_rm = Role::where('name', 'Dealer')->get()->first();

        $rmIds = $role_rm->users()->pluck('id')->toArray();

        $query = DB::table('users as u')
            ->leftJoin('clients as c', 'c.rm_id', '=', 'u.id')
            ->leftJoin('clients_scripts as cs', 'cs.client_id', '=', 'c.id')
            ->leftJoin('scripts as s', 'cs.script_id', '=', 's.id')
            ->whereRaw('c.rm_id IN ('. implode(',', $rmIds) .')');


        return $query;

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
