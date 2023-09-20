<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property string $name
 * @property string $description
 * Relationships
 * @property Collection $users
 * @property Collection $timeLogs
 */
class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $fillable = ['title', 'description'];

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TimeLog::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }
}
