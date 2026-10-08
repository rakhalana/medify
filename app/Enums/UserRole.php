<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case VENDOR = 'vendor';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::VENDOR => 'Vendor Toko',
            self::ADMIN => 'Administrator',
        };
    }
}
