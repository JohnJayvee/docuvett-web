<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Audit;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Audit\Audit
 *
 * @property int $id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property int $auditable_id
 * @property string|null $old_values
 * @property string|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Audit[] $auditable
 * @property-read mixed $fields
 * @property-read Collection|Audit[] $user
 * @method static Builder|Audit newModelQuery()
 * @method static Builder|Audit newQuery()
 * @method static Builder|Audit query()
 * @method static Builder|Audit whereAuditableId($value)
 * @method static Builder|Audit whereAuditableType($value)
 * @method static Builder|Audit whereCreatedAt($value)
 * @method static Builder|Audit whereEvent($value)
 * @method static Builder|Audit whereId($value)
 * @method static Builder|Audit whereIpAddress($value)
 * @method static Builder|Audit whereNewValues($value)
 * @method static Builder|Audit whereOldValues($value)
 * @method static Builder|Audit whereTags($value)
 * @method static Builder|Audit whereUpdatedAt($value)
 * @method static Builder|Audit whereUrl($value)
 * @method static Builder|Audit whereUserAgent($value)
 * @method static Builder|Audit whereUserId($value)
 * @method static Builder|Audit whereUserType($value)
 * @mixin Eloquent
 */
class Audit extends Model
{
    protected $appends = [
        'fields'
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }


    public function getFieldsAttribute(): array
    {
        $new_values = [];
        $old_values = [];
        $auditable_type = $this->auditable_type;
        $displayNames = isset($auditable_type::$displayFieldsName)
            ? $auditable_type::$displayFieldsName
            : null;

        if (!(!!$displayNames)) {
            return [
                'old_values' => $this->old_values,
                'new_values' => $this->new_values
            ];
        }

        foreach ($this->new_values as $key => $value) {
            $displayName = isset($displayNames[$key]) ? $displayNames[$key] : $key;
            if (isset($this->new_values->$key)) {
                $new_values[$displayName] = $this->new_values->$key;
            } else {
                $new_values[$key] = $value;
            }
        }
        foreach ($this->old_values as $key => $value) {
            $displayName = isset($displayNames[$key]) ? $displayNames[$key] : $key;
            if (isset($this->old_values->$key)) {
                $old_values[$displayName] = $this->old_values->$key;
            } else {
                $old_values[$key] = $value;
            }
        }
        return [
            'old_values' => $old_values,
            'new_values' => $new_values
        ];
    }
}
