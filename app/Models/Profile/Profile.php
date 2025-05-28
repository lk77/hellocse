<?php

namespace App\Models\Profile;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string|null $image_original_name
 * @property string|null $image_name
 * @property string $status
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Database\Factories\Profile\ProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereImageOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Profile extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\Profile\ProfileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'image_original_name',
        'image_name',
        'status',
        'user_id',
    ];

    /**
     * Recover the user
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
