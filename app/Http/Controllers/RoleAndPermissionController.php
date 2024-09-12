<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Requests\Role\UpdateRolePermissionRequest;
use App\Interfaces\RoleAndPermissionRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleAndPermissionController extends Controller
{
    private RoleAndPermissionRepository $roleAndPermissionRepository;

    public function __construct(RoleAndPermissionRepository $roleAndPermissionRepository) {
        $this->roleAndPermissionRepository = $roleAndPermissionRepository;
    }

    public function roleAndPemissionManagePage(Request $request)
    {
        return Inertia::render('User/RoleAndPermissionManage', [
            'roles' => $this->roleAndPermissionRepository->getRoles(),
        ]);
    }
    
    public function create(RoleRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->roleAndPermissionRepository->createRole($validated);
            return redirect()->back()->with('message','Success to create role');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message'=>$th->getMessage()]);
        }
    }

    public function update(int $roleId, RoleRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->roleAndPermissionRepository->updateRole($roleId, $validated);
            return redirect()->back()->with('message','Success to update role');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message'=>$th->getMessage()]);
        }
    }
    
    public function delete(int $roleId, Request $request)
    {
        try {
            $this->roleAndPermissionRepository->deleteRole($roleId);
            return redirect()->back()->with('message','Success to update role');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message'=>$th->getMessage()]);
        }
    }

    public function getRolePermission(int $roleId, Request $request)
    {
        try{
            $permissionData = $this->roleAndPermissionRepository->getRolePermission($roleId);
            return response()->json([
                'status' => true,
                'message' => 'Success to get permission list',
                'data' => $permissionData,
            ],200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ],500);
        }
    }

    public function getRoleUser(int $roleId, Request $request)
    {
        try{
            $userData = $this->roleAndPermissionRepository->getRoleUser($roleId);
            return response()->json([
                'status' => true,
                'message' => 'Success to get user list',
                'data' => $userData,
            ],200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ],500);
        }
    }

    public function switchPermission(int $roleId, UpdateRolePermissionRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->roleAndPermissionRepository->switchPermission($roleId, $validated['id_permission'],$validated['value']);
            return response()->json([
                'status' => true,
                'message' => 'Successfully updated role permissions',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
