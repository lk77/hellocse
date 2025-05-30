<?php

namespace App\Http\Requests\Api\Profile\Comment;

use App\Models\Profile\Comment;
use App\Rules\Profile\UniqueComment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check() && $this->user('api')->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1024',
            'profile_id' => ['required', 'integer', app(UniqueComment::class)],
        ];
    }
}
