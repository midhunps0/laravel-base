<?php
namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function index(
        $itemsCount,
        $page,
        $searches,
        $sorts,
        $filters,
        $selectedIds
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
        $users = $queryData['query']
            ->whereHas('roles', function ($query) {
                $query->where('name', '<>', 'Super Admin');
            })
            ->paginate(
                $itemsCount,
                ['*'],
                'page',
                $page
            );

        $itemIds = $users->pluck('id')->toArray();
        $data = $users->toArray();

        return [
            'users' => $users,
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => implode(',',$itemIds),
            'total_results' => $data['total'],
            'current_page' => $data['current_page']
        ];
    }

    public function processDownload(
        $searches,
        $sorts,
        $filters,
        $selectedIds
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
        $users = $queryData['query']->get();
        return $users;
    }

    public function getIdsForParams(
        $searches,
        $sorts,
        $filters
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters
        );

        $users = $queryData['query']->get()->pluck('id');
        return $users;
    }

    private function getQueryAndParams(
        $searches,
        $sorts,
        $filters,
        $selectedIds = ''
    ) {
        $searchParams = [];
        $sortParams = [];
        $filterData = [];

        $query = User::with('roles');

        foreach ($searches as $search) {
            $data = explode('::', $search);
            $query->where($data[0], 'like', '%'.$data[1].'%');
            $searchParams[$data[0]] = $data[1];
        }
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $query->orderBy($data[0], $data[1]);
            $sortParams[$data[0]] = $data[1];
        }
        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            if ($data[0] == 'roles') {
                $query->withRoles([$data[1]]);
            }
            $filterData[$data[0]]['selected'] = $data[1];
        }
        $filterData['roles']['options'] = Role::all();

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $query->whereIn('id', $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData
        ];
    }

    public function store($data)
    {
        $fields = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ];
        if(isset($data['tl_id'])) {
            $fields['teamleader_id'] = intval($data['tl_id']);
        }
        info($fields);
        $user = User::create($fields);
        $user->assignRole(intval($data['role_id']));
        return [
            'success' => true,
            'message' => 'New user added.'
        ];
    }

    public function update($user, $data)
    {
        $fields = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ];
        if(isset($data['tl_id'])) {
            $fields['teamleader_id'] = intval($data['tl_id']);
        }
        $user->update($fields);
        $user->roles()->sync([intval($data['role_id'])]);
        return true;
    }

    public function destroy($id)
    {
        $user = User::withcount('dealers', 'clients')->where('id', $id)->get()->first();
        $result = false;
        $message = '';
        if ($user->dealers_count > 0) {
            $message = "Unable to delete the user. There are dealers assigned to this user.";
        } elseif ($user->clients_count > 0) {
            $message = "Unable to delete the user. There are clients assigned to this user.";
        } else {
            DB::beginTransaction();
            try {
                $user->roles()->sync([]);
                $user->teamleader()->delete();
                $user->delete();
                $message = "user deleted!";
                $result = true;
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
                $message = "Unexpected Error. Failed to delete user.";
            }
        }
        return [
            'success' => $result,
            'message' => $message
        ];
    }
}
?>