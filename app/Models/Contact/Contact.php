<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Contact;

use App\Models\Company\Company;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

/**
 * App\Models\Contact\Contact
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read Collection|Company[] $companies
 * @property-read int|null $companies_count
 * @property Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read User $user
 * @method static Builder|Contact newModelQuery()
 * @method static Builder|Contact newQuery()
 * @method static Builder|Contact query()
 * @method static Builder|Contact whereCreatedAt($value)
 * @method static Builder|Contact whereEmail($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereName($value)
 * @method static Builder|Contact wherePhone($value)
 * @method static Builder|Contact whereUpdatedAt($value)
 * @method static Builder|Contact whereUserId($value)
 * @method static Builder|Contact withAllTags($tags, ?string $type = null)
 * @method static Builder|Contact withAllTagsOfAnyType($tags)
 * @method static Builder|Contact withAnyTags($tags, ?string $type = null)
 * @method static Builder|Contact withAnyTagsOfAnyType($tags)
 * @mixin Eloquent
 */
class Contact extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasTags;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
}
