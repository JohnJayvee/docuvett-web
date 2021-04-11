<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Module;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Module\Module
 *
 * @property string $id
 * @property string $name
 * @property int $enabled
 * @property int $free
 * @property int $one_time
 * @property string $payment_date
 * @property string $expires_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Module newModelQuery()
 * @method static Builder|Module newQuery()
 * @method static Builder|Module query()
 * @method static Builder|Module whereCreatedAt($value)
 * @method static Builder|Module whereEnabled($value)
 * @method static Builder|Module whereExpiresDate($value)
 * @method static Builder|Module whereFree($value)
 * @method static Builder|Module whereId($value)
 * @method static Builder|Module whereName($value)
 * @method static Builder|Module whereOneTime($value)
 * @method static Builder|Module wherePaymentDate($value)
 * @method static Builder|Module whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Module extends Model
{
    protected $table = 'modules';

    public $incrementing = false;

    public $grace_period;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'enabled',
        'free',
        'one_time',
        'payment_date',
        'expires_date',
    ];

    protected $appends = [
        'status',
    ];

    public function getStatusAttribute(): bool
    {
        return $this->enabled && ($this->free || $this->one_time || $this->expires_date > now() || $this->isGracePeriod());
    }

    public function getGracePeriod(): Carbon
    {
        return Carbon::parse($this->expires_date)->addDays(config('stripe.grace_period'));
    }

    public function isGracePeriod(): bool
    {
        return $this->getGracePeriod() > Carbon::now() && !$this->free;
    }

    public static function generateId($name): string
    {
        return self::getIdPrefix() . $name;
    }

    public static function getDefaultAttributes($name): array
    {
        return [
            'id' => self::generateId($name),
            'name' => $name,
            'enabled' => true,
            'free' => true,
            'one_time' => true,
        ];
    }

    public static function createModule($name)
    {
        return self::create(self::getDefaultAttributes($name));
    }

    public static function getIdPrefix()
    {
        $clientName = config('stripe.client_name');
        return $clientName . '_';
    }

    public static function isModuleInConfig($name): bool
    {
        $configModules = collect(config('modular.modules'))->flatten(1)->pluck('name')->filter();
        return $configModules->contains($name);
    }
}
