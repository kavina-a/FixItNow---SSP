<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrator  = 'administrator';
    case ServiceProvider = 'service_provider';
    case Customer = 'customer';
}

