<?php

namespace App\Repositories;

use App\Interfaces\RoleAndPermissionRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionRepositoryImpl implements RoleAndPermissionRepository
{
    private const SUPERADMIN_ROLE_ID = 1;

    public function getRoles()
    {
        return Role::all();
    }
    public function createRole(array $roleData)
    {
        try {
            Role::create($roleData);
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to create role");
        }
    }
    public function updateRole(int $roleId, array $roleData)
    {
        if ($roleId == self::SUPERADMIN_ROLE_ID) {
            throw new \InvalidArgumentException("Superadmin cannot be changed");
        }
        try {
            $role = Role::findOrFail($roleId);
            $role->update($roleData);
            return $role;
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Role not found");
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to update role");
        }
    }
    public function deleteRole(int $roleId)
    {
        if ($roleId == self::SUPERADMIN_ROLE_ID) {
            throw new \InvalidArgumentException("Superadmin cannot be changed");
        }
        try {
            $role = Role::findOrFail($roleId);
            if ($role->users()->count() > 0) {
                throw new \LogicException("Role cannot be deleted because it has users");
            }
            $role->delete();
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Role not found");
        } catch (\LogicException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to delete role");
        }
    }

    public function getRolePermission(int $roleId)
    {
        try {
            $permissions = Permission::select('id', 'name')
                ->leftJoin('role_has_permissions', function ($join) use ($roleId) {
                    $join->on('permissions.id', '=', 'role_has_permissions.permission_id')
                        ->where('role_has_permissions.role_id', '=', $roleId);
                })
                ->addSelect(DB::raw('
                    CASE 
                        WHEN role_has_permissions.role_id IS NOT NULL
                        THEN 1 
                        ELSE 0 
                    END AS role_has_permission'))
                ->orderBy('id')
                ->get();
            $permissions = $permissions->map(function ($p) {
                $p->role_has_permission = intval($p->role_has_permission);
                return $p;
            });
            $permissionsGrouped = $permissions->groupBy(function ($item) {
                return explode('.', $item['name'])[0];
            });
            $data = [
                'permissions' => $permissionsGrouped,
                'total_assigned_permission' => $permissions->where('role_has_permission', 1)->count(),
            ];
            return $data;
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to get role permission list");
        }
    }

    public function getRoleUser(int $roleId)
    {
        try {
            $users = User::with(['roles'])
                    ->whereHas('roles', fn($q)=> $q->where('id', '=', $roleId))
                    ->get();
            
            $data = [
                'users' => $users,
                'user_count' => $users->count(),
            ];
            return $data;
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to get role permission list");
        }
    }

    public function switchPermission(int $roleId, int $permissionId, bool $status)
    {
        if ($roleId == self::SUPERADMIN_ROLE_ID) {
            throw new \InvalidArgumentException("Superadmin cannot be changed");
        }
        try {
            $role = Role::findOrFail($roleId);
            $permission = Permission::findOrFail($permissionId);
            if($status){
                $role->givePermissionTo($permission);
            }else{
                $role->revokePermissionTo($permission);
            }
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Role or permission not found");
        } catch (\Throwable $th) {
            throw new \RuntimeException("Failed to switch permission");
        }
    }
}
