<?php

namespace App\Interfaces;

interface UserRepository
{
    public function getUserDataProcessingWith(array $with = null);
    public function createUser(array $userData);
    public function updateUser(String $userUuid, array $userData);
    public function deleteUser(String $userUuid);
    public function syncLeader();
    public function syncPlt();
}
