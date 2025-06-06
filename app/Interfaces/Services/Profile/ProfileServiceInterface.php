<?php

namespace App\Interfaces\Services\Profile;

use App\Data\Profile\ProfileData;

interface ProfileServiceInterface
{
    public function get(int $id): ProfileData;

    /**
     * @return \Illuminate\Support\Collection<int, ProfileData>
     */
    public function getAll(): \Illuminate\Support\Collection;

    public function create(ProfileData $data): ProfileData;

    public function update(ProfileData $data): ProfileData;

    public function delete(ProfileData $data): bool;
}
