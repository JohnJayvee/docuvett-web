<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models\Setting;

use Cache;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Setting\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $type
 * @property int $is_hidden
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereIsHidden($value)
 * @method static Builder|Setting whereName($value)
 * @method static Builder|Setting whereType($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 */
class Setting extends Model
{

    const CONFIG_KEY_COMPANY = 'company';
    const CONFIG_KEY_APPOINTMENT = 'appointment';
    const CONFIG_KEY_CONTACT = 'contact';
    const CONFIG_KEY_CONTACTS = 'contacts';

    const CONFIG_KEY_COMPANY_GENERAL = 'general';
    const CONFIG_KEY_APPOINTMENT_TIME = 'time';
    const CONFIG_KEY_CONTACT_GENERAL = 'general';
    const CONFIG_KEY_CONTACTS_GENERAL = 'general';

    const CONTACT_SECOND_ADDRESS = 'contact_second_address';
    const CONTACT_BIRTHDAY = 'contact_birthday';
    const CONTACT_SITE_EVENT = 'contact_site_event';
    const CONTACT_ABN = 'abn';
    const WASH_LEADS = 'wash_leads';
    const TRANSFER = 'transfer';

    const FIELD_SETTING_TITLE = 'title';
    const FIELD_SETTING_DESCRIPTION = 'description';
    const FIELD_SETTING_ICON = 'icon';
    const FIELD_SETTING_ELEMENTS = 'elements';

    const COLUMN_NAME = 'name';
    const COLUMN_VALUE = 'value';
    const COLUMN_TYPE = 'type';
    const COLUMN_IS_HIDDEN = 'is_hidden';

    const FIELD_TYPE = self::COLUMN_TYPE;
    const FIELD_DATA = 'data';
    const FIELD_NAME = self::COLUMN_NAME;
    const FIELD_LABEL = 'label';
    const FIELD_RULES = 'rules';
    const FIELD_CLASS = 'class';
    const FIELD_VALUE = self::COLUMN_VALUE;
    const FIELD_IS_HIDDEN = self::COLUMN_IS_HIDDEN;

    const FIELD_TYPE_TIME = 'time';
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_TEXT = 'text';

    const VALUE_NAME_FIRST_APPOINTMENT_OF_DAY = 'first_appointment_of_day';
    const VALUE_NAME_SECOND_APPOINTMENT_OF_DAY = 'second_appointment_of_day';
    const VALUE_NAME_THIRD_APPOINTMENT_OF_DAY = 'third_appointment_of_day';
    const VALUE_NAME_FOURTH_APPOINTMENT_OF_DAY = 'fourth_appointment_of_day';

    const VALUE_LABEL_FIRST_APPOINTMENT_OF_DAY = 'First Appointment of Day';
    const VALUE_LABEL_SECOND_APPOINTMENT_OF_DAY = 'Second Appointment of Day';
    const VALUE_LABEL_THIRD_APPOINTMENT_OF_DAY = 'Third Appointment of Day';
    const VALUE_LABEL_FOURTH_APPOINTMENT_OF_DAY = 'Fourth Appointment of Day';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Add a settings value
     * @param string $key
     * @param string $val
     * @param string $type
     * @param bool $hidden
     * @return bool
     */
    public static function add(string $key, string $val, string $type = self::FIELD_TYPE_STRING, bool $hidden = false)
    {
        if (self::has($key)) {
            return self::set($key, $val, $type);
        }

        return self::create([
            self::FIELD_NAME => $key,
            self::FIELD_VALUE => $val,
            self::FIELD_TYPE => $type,
            self::FIELD_IS_HIDDEN => $hidden
        ]) ? $val : false;
    }

    /**
     * Get a settings value
     *
     * @param $key
     * @param null $default
     * @return bool|int|mixed
     */
    public static function get($key, $default = null)
    {
        if (self::has($key)) {
            $setting = self::getAllSettings()->where(self::COLUMN_NAME, $key)->first();
            return self::castValue($setting->value, $setting->type);
        }

        return self::getDefaultValue($key, $default);
    }

    /**
     * Set a value for setting
     * @param string $key
     * @param string $val
     * @param string $type
     * @param bool $hidden
     * @return bool|string
     */
    public static function set(string $key, string $val, string $type = self::FIELD_TYPE_STRING, bool $hidden = false)
    {
        if ($setting = self::getAllSettings()->where(self::COLUMN_NAME, $key)->first()) {
            return $setting->update([
                self::FIELD_NAME => $key,
                self::FIELD_VALUE => $val,
                self::FIELD_TYPE => $type,
                self::FIELD_IS_HIDDEN => $hidden
            ]) ? $val : false;
        }

        return self::add($key, $val, $type, $hidden);
    }

    /**
     * Remove a setting
     *
     * @param $key
     * @return bool
     * @throws Exception
     */
    public static function remove($key): bool
    {
        if (self::has($key)) {
            return self::whereName($key)->delete();
        }

        return false;
    }

    /**
     * Check if setting exists
     *
     * @param $key
     * @return bool
     */
    public static function has($key): bool
    {
        return (bool)self::getAllSettings()->whereStrict(self::FIELD_NAME, $key)->count();
    }

    /**
     * Get the validation rules for setting fields
     *
     * @param string $settingsName
     * @return array
     */
    public static function getValidationRules(string $settingsName): array
    {
        return self::getDefinedSettingFields($settingsName)->pluck(self::FIELD_RULES, self::FIELD_NAME)
            ->reject(function ($val) {
                return is_null($val);
            })->toArray();
    }

    /**
     * Get the data type of a setting
     *
     * @param string $settingName
     * @param string $field
     * @return mixed
     */
    public static function getDataType(string $settingName, string $field): string
    {
        $type = self::getDefinedSettingFields($settingName)
            ->pluck(self::FIELD_DATA, self::FIELD_NAME)
            ->get($field);

        return is_null($type) ? self::FIELD_TYPE_STRING : $type;
    }

    /**
     * Get default value for a setting
     *
     * @param $field
     * @return mixed
     */
    public static function getDefaultValueForField($field)
    {
        return self::getDefinedSettingFields($field)
            ->pluck(self::FIELD_VALUE, self::FIELD_NAME)
            ->get($field);
    }

    /**
     * Get default value from config if no value passed
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    private static function getDefaultValue($key, $default)
    {
        return is_null($default) ? self::getDefaultValueForField($key) : $default;
    }

    /**
     * Get all the settings fields from config
     *
     * @param string $settingsName
     * @return Collection
     */
    public static function getDefinedSettingFields(string $settingsName): Collection
    {
        return collect(config("settings_fields.app.$settingsName"))->pluck(self::FIELD_SETTING_ELEMENTS)->flatten(1);
    }

    /**
     * caste value into respective type
     *
     * @param $val
     * @param $castTo
     * @return bool|int
     */
    private static function castValue($val, $castTo)
    {
        switch ($castTo) {
            case 'int':
            case 'integer':
                return intval($val);

            case 'bool':
            case 'boolean':
                return boolval($val);

            default:
                return $val;
        }
    }

    /**
     * Get all the settings
     *
     * @param bool $includingHidden Fetch all setting including hidden
     * @return mixed
     */
    public static function getAllSettings($includingHidden = true)
    {
        return Cache::rememberForever('settings.all', function () {
            return self::all();
        })->filter(function ($value) use ($includingHidden) {
            return $includingHidden ? true : !$value->is_hidden;
        });
    }

    /**
     * Flush the cache
     */
    public static function flushCache()
    {
        Cache::forget('settings.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });
    }
}
