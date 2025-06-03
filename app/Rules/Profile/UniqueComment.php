<?php

namespace App\Rules\Profile;

use App\Interfaces\Services\Profile\CommentServiceInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webmozart\Assert\Assert;

readonly class UniqueComment implements ValidationRule
{
    public function __construct(
        private CommentServiceInterface $commentService
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = auth('api')->id();
        Assert::integer($userId);
        Assert::integer($value);

        if ($attribute === 'profile_id' && ! $this->commentService->check($value, $userId)) {
            $fail('There is already a comment on this profile.');
        }
    }
}
