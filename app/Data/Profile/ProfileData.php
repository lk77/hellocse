<?php

namespace App\Data\Profile;

use App\Enums\Profile\ProfileStatus;
use App\Models\User\User;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Webmozart\Assert\Assert;

class ProfileData extends Data
{
    public function __construct(
        public ?int $id,
        public string $firstname,
        public string $lastname,
        public ?UploadedFile $image,
        public ?string $image_original_name,
        public ?string $image_name,
        public ProfileStatus $status,
        public User $user
    ) {
        if (is_null($this->image)) {
            Assert::stringNotEmpty($this->image_original_name);
            Assert::stringNotEmpty($this->image_name);
        }
    }
}
