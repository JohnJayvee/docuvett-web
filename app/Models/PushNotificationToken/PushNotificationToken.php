<?php

namespace App\Models\PushNotificationToken;

use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\PushNotificationToken\PushNotificationToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|PushNotificationToken newModelQuery()
 * @method static Builder|PushNotificationToken newQuery()
 * @method static Builder|PushNotificationToken query()
 * @method static Builder|PushNotificationToken whereCreatedAt($value)
 * @method static Builder|PushNotificationToken whereId($value)
 * @method static Builder|PushNotificationToken whereToken($value)
 * @method static Builder|PushNotificationToken whereUpdatedAt($value)
 * @method static Builder|PushNotificationToken whereUserId($value)
 * @mixin Eloquent
 */
class PushNotificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
