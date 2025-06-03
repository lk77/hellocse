<?php

namespace App\Http\Requests\Api\Profile\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check() && $this->user('api')?->can('update', $this->route('comment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content'    => 'required|string|max:1024',
            'profile_id' => 'required|integer',
        ];
    }
}
