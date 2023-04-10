<?php

namespace App\Models;

use App\Casts\DynamicCast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    /**
     * The attributes that should be fillable
     *
     * @var array
     */
    protected $fillable = ['key','value'];


    /**
     * Whether timestamps should be used.
     *
     * @var array
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => DynamicCast::class,
    ];

    /**
     * Add a way to clear the subscriber cache when settings are changed.
     */
    public static function boot()
    {
        parent::boot();

        // clear the subscribers cache on setting change.
        Setting::saved(function () {
            Cache::tags('subscribers')->flush();
        });
        
        // clear the subscribers cache on setting change.
        Setting::deleted(function () {
            Cache::tags('subscribers')->flush();
        });
    }

    /**
     * Get or create the per page setting.
     * @return integer
     */
    public static function getSubscribersPerPage()
    {
        return self::firstOrCreate(['key'=> 'per_page'], ['value'=>10])->value;
    }
    
    /**
     * Get the API key
     * @return string
     */
    public static function getAPIKey()
    {
        $data =  self::where([
            ['key', '=', 'api_key'],
            ['value', '<>', ''],
        ])->first();

        if ($data) {
            return $data->value;
        }

        return false;
    }

    /**
     * Check if the API key exists
     * @return boolean
     */
    public static function hasAPIKey()
    {
        return self::where([
            ['key', '=', 'api_key'],
            ['value', '<>', ''],
        ])->exists();
    }
}
