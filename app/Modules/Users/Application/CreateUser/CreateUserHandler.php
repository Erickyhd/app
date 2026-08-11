<?php

namespace App\Modules\Users\Application\CreateUser;

use App\Modules\Users\Domain\User;
use App\Modules\Users\Domain\UserRepository;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;

class CreateUserHandler
{
    public function __construct(private UserRepository $repository) {}

    public function handle(CreateUserCommand $command): User
    {
        $existingUser = $this->repository->findByEmail($command->email);
        
        if ($existingUser !== null) {
            throw new UserAlreadyExistsException("El usuario con el email {$command->email} ya existe.");
        }

        // En la arquitectura hexagonal real, hashear el password se puede hacer aquí o a través de un servicio de dominio.
        // Asumimos que viene en texto plano y lo hasheamos.
        $hashedPassword = password_hash($command->password, PASSWORD_BCRYPT);

        $user = new User(
            null,
            $command->name,
            $command->email,
            $hashedPassword
        );

        return $this->repository->save($user);
    }
}
