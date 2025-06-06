<?php

namespace App\Http\Requests\Api\Profile;

use App\Enums\Profile\ProfileStatus;
use App\Models\Profile\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check() && $this->user('api')?->can('create', Profile::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpeg,png',
            'status'    => [
                'required',
                Rule::in(collect(ProfileStatus::cases())->map->name),
            ],
        ];
    }
}
