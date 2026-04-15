<?php

namespace App\Models;

use App\Shared\Infrastructure\Concerns\UsesCentralConnection;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UsesCentralConnection;
}
