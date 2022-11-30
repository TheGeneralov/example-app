<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Роли пользователя
 *
 * @method static ADMIN
 * @method static MANAGER
 * @method static CLIENT
 * @method static WAITER
 * @method static SUPPORT
 * @method static APPLICATION
 */
class RolesEnum extends BaseEnum
{
    /** Администратор */
    const ADMIN = 1;
    /** Менеджер */
    const MANAGER = 2;
    /** Клиент */
    const CLIENT = 3;
    /** Официант */
    const WAITER = 4;
    /** Тех. поддержка */
    const SUPPORT = 5;
    /** Приложение (???) */
    const APPLICATION = 6;
}

