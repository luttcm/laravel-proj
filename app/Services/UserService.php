<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PictureRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    protected $userRepository;
    protected $pictureRepository;

    public function __construct(UserRepository $userRepository, PictureRepository $pictureRepository)
    {
        $this->userRepository = $userRepository;
        $this->pictureRepository = $pictureRepository;
    }

    public function createUser(array $data)
    {
        $password = $data['password'] ?? Str::random(12);

        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'role' => $data['role'],
        ]);

        $this->pictureRepository->create([
            'path' => 'avatars/classicAvatar.png',
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);

        return [
            'user' => $user,
            'password' => $password
        ];
    }
    
    public function updateUser(int $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id)
    {
        return $this->userRepository->delete($id);
    }
    
    public function updateAvatar(int $userId, string $path)
    {
        $this->pictureRepository->deleteByEntity('user', $userId);
        
        return $this->pictureRepository->create([
            'path' => 'storage/' . $path,
            'entity_type' => 'user',
            'entity_id' => $userId,
        ]);
    }
}
