<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDeleteRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Validation\Rule;
use App\ImportExports\UserExports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class UserController extends SmartController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserService $userService)
    {
        $data = $userService->index(
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );

        return $this->buildResponse('admin.users.index', $data);
    }

    public function queryIds(UserService $userService)
    {
        $ids = $userService->getIdsForParams(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', [])
        );

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }

    public function download(UserService $userService)
    {
        $users = $userService->processDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );
        return Excel::download(new UserExports($users), 'invoices.xlsx');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('name', '<>', 'Super Admin')->get();
        $tls = User::withRoles(['Team Leader'])->get();
        $dlrid = Role::where('name', 'Dealer')->get()->first()->id;
        return $this->buildResponse('admin.users.create',
            ['roles' => $roles, 'tls' => $tls, 'dlrid' => $dlrid]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, UserService $userService)
    {
        $dlrid = Role::where('name', 'Dealer')->get()->first()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|integer|min:1',
            'tl_id' => 'required_if:role_id,'.$dlrid.'|integer|min:1'
        ], $messages = [
            'tl_id.required_if' => 'Team Leader field is required for adding a Dealer.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ]);
        }
        $result = $userService->store($validator->validated());
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::where('name', '<>', 'Super Admin')->get();
        $tls = User::withRoles(['Team Leader'])->get();
        $dlrid = Role::where('name', 'Dealer')->get()->first()->id;
        return $this->buildResponse('admin.users.edit',
            ['user' => $user, 'roles' => $roles, 'tls' => $tls, 'dlrid' => $dlrid]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, UserService $userService)
    {
        $dlrid = Role::where('name', 'Dealer')->get()->first()->id;
        $tlid = Role::where('name', 'Team Leader')->get()->first()->id;
        $user = User::where('id', $id)->withCount('dealers')->get()->first();
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'username' => ['required', 'string', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($user, $tlid){
                    if ($user->roles[0]->name == 'Team Leader' && intval($value) != $tlid && $user->dealers_count > 0) {
                        $fail('Cannot change the role \'Team Leader\'. The user currently has dealers assigned.');
                    }
                }
            ],
            'tl_id' => ['required_if:role_id,'.$dlrid, 'integer', 'min:1']
        ], $messages = [
            'tl_id.required_if' => 'Team Leader field is required for adding a Dealer.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ]);
        }
        $result = $userService->update($user, $validator->validated());
        return response()->json([
            'success' => true,
            'error' => ''
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDeleteRequest $request, $id, UserService $userService)
    {
        $result = $userService->destroy($id);

        return response()->json($result);
    }

    // private function getUniqueParams(Collection $results, $param, $key)
    // {
    //     $ar = [];
    //     $ids = [];
    //     foreach ($results as $result) {
    //         if (!in_array($result->$param->$key, $ids)) {
    //             $ar = array_push($ar, $result->$param);
    //             $ids = array_push($ids, $resul->$param->$key);
    //         }
    //     }
    //     return $ar;
    // }
}
