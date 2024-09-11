<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\PasswordChangeRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Interfaces\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxResponse;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function userManagePage(Request $request)
    {
        return Inertia::render('User/UserManage', [
            'roles' => Role::all(),
            'leader_enabled' => config('leader.LEADER_API_KEY') != null,
        ]);
    }
    public function dataProcessing(Request $request)
    {
        $loadData = $this->userRepository->getUserDataProcessingWith(['roles']);
        return DxResponse::json($loadData, $request);
    }
    public function create(CreateUserRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->userRepository->createUser($validated);
            return redirect()->back()->with('message', 'Success to create user');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function update(String $userUuid, UpdateUserRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $this->userRepository->updateUser($userUuid,$validated);
            return redirect()->back()->with('message', 'Success to update user');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function delete(String $userUuid, Request $request)
    {
        try {
            $this->userRepository->deleteUser($userUuid);
            return redirect()->back()->with('message', 'Success to delete user');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function switchStatus(String $userUuid, Request $request)
    {
        try {
            $userData = ['is_active' => $request['is_active'] ? 1 : 0];
            $this->userRepository->updateUser($userUuid,$userData);
            return redirect()->back()->with('message', 'Success to switch user status');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function changePassword(String $userUuid, PasswordChangeRequest $request)
    {
        $validated = $request->validated();
        try {
            $userData = ['password' => $validated['new_password']];
            $this->userRepository->updateUser($userUuid,$userData);
            return redirect()->back()->with('message', 'Success to change password');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function syncLeader(Request $request)
    {
        set_time_limit(0);
        try {
            $this->userRepository->syncLeader();
            return redirect()->back()->with('message', 'Success sync leader');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
    public function syncPlt(Request $request)
    {
        set_time_limit(0);
        try {
            $this->userRepository->syncPlt();
            return redirect()->back()->with('message', 'Success sync plt');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
}
