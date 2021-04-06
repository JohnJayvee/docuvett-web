<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Company;

use App\Models\Contact\Contact;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\Models\Company\Company
 *
 * @property int $id
 * @property string $name
 * @property string $abn
 * @property string $email
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read Collection|Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @method static Builder|Company whereAbn($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereEmail($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company wherePhone($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company withAllTags($tags, ?string $type = null)
 * @method static Builder|Company withAllTagsOfAnyType($tags)
 * @method static Builder|Company withAnyTags($tags, ?string $type = null)
 * @method static Builder|Company withAnyTagsOfAnyType($tags)
 * @mixin Eloquent
 */
class Company extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasTags;
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'abn',
    ];

    public function users(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->contacts(), (new Contact())->user());
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class);
    }
}
