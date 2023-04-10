<?php

/**
 * A custom cast type to cast variables to their potential types.
 * I created this, because I was considering adding a columns setting
 * which would have needed to store an array of values.
 */
 
namespace App\Casts;
 
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
 
class DynamicCast implements CastsAttributes
{
    
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($this->isJSON($value)) {
            return json_decode($value, true);
        } elseif (is_numeric($value)) {
            return floatval($value);
        }
        
        return $value;
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    /**
    * The function checks if a given value is a valid JSON string.
    *
    * @param string $value The parameter that needs to be checked if it is a valid JSON string or not.
    *
    * @return Boolean
    */
    public function isJSON($value)
    {
        $value = json_decode($value);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }
        return true;
    }
}
