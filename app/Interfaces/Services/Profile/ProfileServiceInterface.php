<?php

namespace App\Interfaces\Services\Profile;

use App\Data\ProfileData;

interface ProfileServiceInterface
{
    public function getAll(): \Illuminate\Support\Collection;

    public function create(ProfileData $data): ProfileData;

    public function update(ProfileData $data): ProfileData;

    public function delete(ProfileData $data): bool;
}
