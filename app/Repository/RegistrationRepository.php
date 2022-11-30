<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\RegistrationDto;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegistrationRepository
{
    public function findUser(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @throws Exception
     */
    public function createShopAndShopManager(RegistrationDto $dto): void
    {
        $userId = User::insertGetId([
            'name' => $dto->getUsername(),
            'email' => $dto->getEmail(),
            'roles_id' => $dto->getRole(),
            'password' => Hash::make($dto->getPassword()),
            'phone' => $dto->getPhone(),
            'messenger' => MessengerEnum::TELEGRAM
        ]);

        $configId = ConfigBest2Pay::where('environment', EnvironmentEnum::PRODUCTION)->first()?->id;
        if (!$configId) {
            throw new Exception('Не найден тип конфигурации ' . EnvironmentEnum::PRODUCTION);
        }

        $shopId = Shop::insertGetId([
            'users_id' => $userId,
            'name' => $dto->getShopName(),
        ]);

        Settings::create([
            'name' => __('settings.show_tips'),
            'slug' => 'show_tips',
            'value' => 1,
        ]);

        $user = User::find($userId);
        $user->last_shop_id = $shopId;
        $user->save();
    }
}
