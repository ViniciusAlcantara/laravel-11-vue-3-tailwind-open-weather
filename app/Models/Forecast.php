<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * [Description Forecast]
 */
class Forecast extends Model
{
    use HasFactory;

    protected $table = 'forecasts';
    protected $primaryKey = 'id';
    public $incrementing = true;

    public $fillable = [
        'user_id',
        'location',
        'date_timestamp',
        'date_txt',
        'temp_max',
        'temp_min',
        'weather',
        'weather_description',
        'weather_icon',
        'forecasts'
    ];

    /**
     * Get the user associated with the user.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
