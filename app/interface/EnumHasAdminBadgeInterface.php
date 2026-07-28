<?php

namespace App\interface;

interface EnumHasAdminBadgeInterface
{

    public function getAdminBadgeClass(): string;
    public function getAdminBadge(): string;
}
