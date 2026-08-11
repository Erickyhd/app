<?php

namespace App\Modules\Users\Infrastructure\Persistence;

use App\Modules\Users\Domain\User;
use App\Modules\Users\Domain\UserRepository;

class MysqlUserRepository implements UserRepository
{
    public function save(User $user): User
    {
        $model = UserModel::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);

        return new User(
            $model->id,
            $model->name,
            $model->email,
            $model->password
        );
    }

    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);
        
        if (!$model) {
            return null;
        }

        return new User($model->id, $model->name, $model->email, $model->password);
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();
        
        if (!$model) {
            return null;
        }

        return new User($model->id, $model->name, $model->email, $model->password);
    }

    public function getAll(): array
    {
        return UserModel::all()->map(function ($model) {
            return new User($model->id, $model->name, $model->email, $model->password);
        })->toArray();
    }

    public function delete(int $id): void
    {
        UserModel::destroy($id);
    }
}
