<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case CMS = 'CMS';
    case ECOMMERCE = 'E-Commerce';
    case MARKETING = 'Marketing';
    case USERS = 'Users & Roles';
    case SETTINGS = 'Settings';
    case REPORTS = 'Reports';
}
