<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Tag;

use App\Models\Company\Company;
use App\Models\Contact\Contact;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

/**
 * App\Models\Tag\Tag
 *
 * @property int $id
 * @property array $name
 * @property array $slug
 * @property string|null $type
 * @property int|null $order_column
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Audit[] $audits
 * @property-read mixed $display_name
 * @property-read mixed $display_slug
 * @property-read mixed $translations
 * @method static Builder|\Spatie\Tags\Tag containing($name, $locale = null)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|\Spatie\Tags\Tag ordered($direction = 'asc')
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereOrderColumn($value)
 * @method static Builder|Tag whereSlug($value)
 * @method static Builder|Tag whereType($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|\Spatie\Tags\Tag withType($type = null)
 * @mixin Eloquent
 */
class Tag extends \Spatie\Tags\Tag implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $duplicateCheck = ['name'];

    public $importFields = ['name'];

    public $exportFields = ['id', 'name'];

    // uses $exportFields if left empty
    public $exportHeadings = [];

    public $appends = ['displayName', 'displaySlug'];

    protected $fillable = ['name'];


    public function getDisplayNameAttribute(): array
    {
        return $this->name;
    }

    public function getDisplaySlugAttribute(): array
    {
        return $this->slug;
    }

    public function duplicate($row = null)
    {
        $existingContact = $this::findFromString($row[$this->duplicateCheck[0]]);
        if (!$existingContact) {
            return $existingContact = new $this;
        } else {
            return $existingContact;
        }
    }

    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(Contact::class, 'taggable');
    }

    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'taggable');
    }
}
