<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Picture;
use App\Models\User;

class PictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPath = 'avatars/classicAvatar.png';

        $users = User::all();
        foreach ($users as $user) {
            $exists = Picture::where('entity_type', 'user')
                        ->where('entity_id', $user->id)
                        ->exists();
            if (! $exists) {
                Picture::create([
                    'path' => $defaultPath,
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                ]);
            }
        }
    }
}
