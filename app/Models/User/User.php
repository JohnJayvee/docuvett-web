<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\User;

use App\Models\Contact\Contact;
use App\Models\Permission\Permission;
use App\Models\Role\Role;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $avatar
 * @property int $wizard_finished
 * @property int $suspended
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $trial_ends_at
 * @property-read Collection|Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read Contact|null $contact
 * @property-read mixed $max_user_level
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property Collection|Tag[] $tags
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read int|null $tags_count
 * @method static Builder|User byRoleAndOrdered(string $role)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User orWherePermissionIs($permission = '')
 * @method static Builder|User orWhereRoleIs($role = '', $team = null)
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCardBrand($value)
 * @method static Builder|User whereCardLastFour($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDoesntHavePermission()
 * @method static Builder|User whereDoesntHaveRole()
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePermissionIs($permission = '', $boolean = 'and')
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleIs($role = '', $team = null, $boolean = 'and')
 * @method static Builder|User whereStripeId($value)
 * @method static Builder|User whereSuspended($value)
 * @method static Builder|User whereTrialEndsAt($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereWizardFinished($value)
 * @method static Builder|User withAllTags($tags, ?string $type = null)
 * @method static Builder|User withAllTagsOfAnyType($tags)
 * @method static Builder|User withAnyTags($tags, ?string $type = null)
 * @method static Builder|User withAnyTagsOfAnyType($tags)
 * @mixin Eloquent
 */
class User extends Authenticatable implements JWTSubject, Auditable
{
    use LaratrustUserTrait;
    use Notifiable;
    use Billable;
    use \OwenIt\Auditing\Auditable;
    use HasTags;
    use HasRelationships;

    public $audit_tags = [];
    public $appends = [
        'maxUserLevel',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'suspended',
        'wizard_finished',
        'tags',
    ];
    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'password',
        'remember_token'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    public function companies(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->contact(), (new Contact())->companies());
    }

    /**
     * @return mixed
     * @noinspection PhpUnused
     */
    public function getMaxUserLevelAttribute()
    {
        $rawRoles = config('laratrust_seeder.role_structure');
        $rolesLevels = array_keys($rawRoles);

        return $this->roles
            ->map(function (Role $role) use ($rolesLevels, $rawRoles) {
                $name = $role->name;
                $priority = ($name && isset($rawRoles[$name])) ? array_search($name, $rolesLevels) : null;

                return $priority === null ? min($rolesLevels) : $priority;
            })
            ->max();
    }

    public function getAvatarAttribute($value): string
    {
        return $value ?: asset('/images/avatar-placeholder.png');
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Scope a query to show ordered users by Role.
     *
     * @param Builder $query
     * @param string $role
     *
     * @return Builder
     * @noinspection PhpUnused
     */
    public function scopeByRoleAndOrdered(Builder $query, string $role): Builder
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $query->whereRoleIs($role)->orderBy('name');
    }

    public function generateTags(): array
    {
        return $this->audit_tags;
    }
}
