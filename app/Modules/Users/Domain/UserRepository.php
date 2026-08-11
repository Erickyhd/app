<?php

namespace App\Modules\Users\Domain;

interface UserRepository
{
    public function save(User $user): User;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function getAll(): array;
    public function delete(int $id): void;
}
