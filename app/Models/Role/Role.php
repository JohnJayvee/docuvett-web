<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Role;

use App\Models\Permission\Permission;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Laratrust\Models\LaratrustRole;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

/**
 * App\Models\Role\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property int|null $level
 * @property string|null $dashboard
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read bool $initial
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDashboard($value)
 * @method static Builder|Role whereDescription($value)
 * @method static Builder|Role whereDisplayName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereLevel($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Role extends LaratrustRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $appends = [
        'initial'
    ];

    protected $table = 'roles';

    public $timestamps = true;

    private $initialRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'level',
        'dashboard',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->initialRoles = config('laratrust_seeder.role_structure');
    }

    /**
     * Check if role is initial
     *
     * @return boolean
     */
    public function getInitialAttribute(): bool
    {
        if ($this && $this->name) {
            return !empty($this->initialRoles[$this->name]);
        }

        return false;
    }
}
