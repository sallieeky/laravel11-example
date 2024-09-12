<?php

namespace App\Interfaces;

interface RoleAndPermissionRepository
{
    public function getRoles();
    public function createRole(array $roleData);
    public function updateRole(int $roleId, array $roleData);
    public function deleteRole(int $roleId);
    public function getRolePermission(int $roleId);
    public function getRoleUser(int $roleId);
    public function switchPermission(int $roleId, int $permissionId, bool $value);
}