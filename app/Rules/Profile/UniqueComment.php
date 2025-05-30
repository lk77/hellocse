<?php

namespace App\Rules\Profile;

use App\Interfaces\Services\Profile\CommentServiceInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueComment implements ValidationRule
{
    public function __construct(
        private readonly CommentServiceInterface $commentService
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($attribute === 'profile_id' && ! $this->commentService->check($value, auth('api')->id())) {
            $fail('There is already a comment on this profile.');
        }
    }
}
