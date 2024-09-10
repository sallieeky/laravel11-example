<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\PasswordChangeRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxAdapter;
use Pkt\StarterKit\Helpers\DxResponse;
use Pkt\StarterKit\Helpers\LeaderApi;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function userManagePage(Request $request)
    {
        return Inertia::render('User/UserManage', [
            'roles' => Role::all(),
            'leader_enabled' => config('leader.LEADER_API_KEY') != null,
        ]);
    }
    public function dataProcessing(Request $request)
    {
        $loadData = User::with(['roles'])->select('*');
        $loadDataDx = DxAdapter::load($loadData);
        return DxResponse::json($loadDataDx, $request);
    }
    public function create(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            $user->syncRoles($validated['role']);

            DB::commit();
            return redirect()->back()->with('message', 'Success to create user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message' => 'Failed to create user'
            ]);
        }
    }
    public function update(User $user, UpdateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user->update($validated);
            $user->syncRoles($validated['role']);

            DB::commit();
            return redirect()->back()->with('message', 'Success to update user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message' => 'Failed to update user'
            ]);
        }
    }
    public function delete(User $user, Request $request)
    {
        $user->delete();
        return redirect()->back()->with('message', 'Success to delete user');
    }
    public function switchStatus(User $user, Request $request)
    {
        $user->update([
            'is_active' => $request['is_active'] ? 1 : 0,
        ]);
        return redirect()->back()->with('message', 'Success to switch user status');
    }
    public function changePassword(User $user, PasswordChangeRequest $request)
    {
        $validated = $request->validated();
        $user->update(
            ['password' => $validated['new_password']]
        );
        return redirect()->back()->with('message', 'Success to change password');
    }
    public function syncLeader(Request $request)
    {
        set_time_limit(0);
        DB::beginTransaction();
        try {
            $employees = LeaderApi::getAllEmployee();
            $employees->each(function ($employee) {
                $user = User::query()->where('npk', $employee->USERS_NPK)->first();
                $dataUser = [
                    'name' => $employee->USERS_NAME,
                    'email' => $employee->USERS_EMAIL,
                    'npk' => $employee->USERS_NPK,
                    'username' => $employee->USERS_USERNAME,
                    'hierarchy_code' => $employee->USERS_HIERARCHY_CODE,
                    'position_id' => $employee->USERS_ID_POSISI,
                    'position' => $employee->USERS_POSISI,
                    'work_unit_id' => $employee->USERS_ID_UNIT_KERJA,
                    'work_unit' => $employee->USERS_UNIT_KERJA,
                    'user_flag' => $employee->USERS_FLAG,
                    'user_alias' => $employee->USERS_ALIAS,
                ];
                if($user){
                    $user->update($dataUser);
                }else{
                    $dataUser['is_active'] = false;
                    $dataUser['password'] = '$2y$12$d99.j1wcGCmj6Jt4C8pA5eIw3HTR1DaWTCnch40aioxeWK64OcVJO';
                    $user = User::create($dataUser);
                    $user->assignRole('Viewer');
                }
            });
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Failed to sync leader']);
        }
        DB::commit();
        return redirect()->back()->with('message', 'Success sync leader');
    }
    public function syncPlt(Request $request)
    {
        set_time_limit(0);
        if (class_exists(\App\Models\Plt::class, false)) {
            DB::beginTransaction();
            try {
                $pltData = LeaderApi::getAllPlt();
                $pltData->each(function ($pltDataItem){
                    $plt = app('App\\Models\\Plt')::updateOrCreate([
                        'npk' => $pltDataItem->PERSONNEL_NUMBER
                    ],[
                        'hierarchy_code' => $pltDataItem->HIERARCHY_CODE ?? null,
                        'position' => $pltDataItem->NAMA_POSISI,
                        'position_id' => $pltDataItem->POSISI_ID ?? null,
                        'valid_from' => $pltDataItem->VALID_FROM,
                        'valid_to' => $pltDataItem->VALID_TO,
                        'data_source' => 'leader_api'
                    ]);
                });
            } catch (\Throwable $e) {
                DB::rollBack();
                return redirect()->back()->withErrors(['message' => 'Failed to sync leader']);
            }
            DB::commit();
            return redirect()->back()->with('message', 'Success sync leader');
        }else{
            return redirect()->back()->withErrors(['message' => 'Leader was not initilized']);
        }
    }
}
