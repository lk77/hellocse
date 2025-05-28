<?php

namespace App\Enums\Profile;

enum ProfileStatus: string
{
    case active = 'active';

    case inactive = 'inactive';

    case pending = 'pending';
}
