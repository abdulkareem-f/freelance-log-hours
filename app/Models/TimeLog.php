<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $user_id
 * @property int $project_id
 * @property string $start_time
 * @property string $end_time
 * @property string $notes
 * Relationships
 * @property User $user
 * @property Project $project
 */
class TimeLog extends Model
{
    use HasFactory;

    protected $table = 'time_logs';
    protected $fillable = ['user_id', 'project_id', 'start_time', 'end_time', 'notes'];
    protected $appends = ['hours'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    protected function getHoursAttribute(): string
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);
        if(!$endTime){
            return '';
        }

        $hours = $endTime->diffInHours($startTime);
        $minutes = $endTime->diffInMinutes($startTime) % 60;

        return sprintf("%02d:%02d", $hours, $minutes);
    }

    protected function startTime(): Attribute
    {
        $timezone = auth()->user()->timezone;
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone($timezone)->format('Y-m-d H:i:s'),
            set: fn($value) => Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i:s'),
        );
    }

    protected function endTime(): Attribute
    {
        $timezone = auth()->user()->timezone;
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone($timezone)->format('Y-m-d H:i:s') : null,
            set: fn($value) => $value ? Carbon::parse($value)->timezone($timezone)->setTimezone('UTC')->format('Y-m-d H:i:s') : null,
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
