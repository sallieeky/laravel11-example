<?php

namespace App\Repositories;

use App\Interfaces\UserRepository;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Pkt\StarterKit\Helpers\DxAdapter;
use Pkt\StarterKit\Helpers\LeaderApi;

class UserRepositoryImpl implements UserRepository{
    public function getUserDataProcessingWith(?array $with = null)
    {
        $data = User::with(['roles'])->select('*');
        $loadData = DxAdapter::load($data);
        return $loadData;
    }

    public function createUser(array $userData)
    {
        DB::beginTransaction();
        try {
            $userData['password'] = Hash::make($userData['password']);
            $user = User::create($userData);
            $user->syncRoles($userData['role']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to create user");
        }
    }

    public function updateUser(String $userUuid, array $userData)
    {
        DB::beginTransaction();
        try {
            $user = User::where('user_uuid',$userUuid)->firstOrFail();
            $user->update($userData);
            if(isset($userData['role'])){
                $user->syncRoles($userData['role']);
            }
            DB::commit();
            return redirect()->back()->with('message', 'Success to update user');
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("User not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to update user");
        }
    }

    public function deleteUser(String $userUuid)
    {
        try {
            $user = User::where('user_uuid',$userUuid)->firstOrFail();
            $user->delete();
            return redirect()->back()->with('message', 'Success to delete user');
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("User not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to delete user");
        }
    }

    public function syncLeader()
    {
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
                if ($user) {
                    $user->update($dataUser);
                } else {
                    $dataUser['is_active'] = false;
                    $dataUser['password'] = '$2y$12$d99.j1wcGCmj6Jt4C8pA5eIw3HTR1DaWTCnch40aioxeWK64OcVJO';
                    $user = User::create($dataUser);
                    $user->assignRole('Viewer');
                }
            });
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to sync leader");
        }
    }

    public function syncPlt()
    {
        if (class_exists(\App\Models\Plt::class, false)) {
            DB::beginTransaction();
            try {
                $pltData = LeaderApi::getAllPlt();
                $pltData->each(function ($pltDataItem) {
                    $plt = app('App\\Models\\Plt')::updateOrCreate([
                        'npk' => $pltDataItem->PERSONNEL_NUMBER
                    ], [
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
                throw new \RuntimeException('Failed to sync plt');
            }
            DB::commit();
        } else {
            throw new \RuntimeException('Leader was not initilized');
        }
    }
}