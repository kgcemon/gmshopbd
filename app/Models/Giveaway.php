<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    protected $table = 'giveaways';

    protected $fillable = [
        'games',
        'prize',
        'status',
        'started_at',
        'finished_at',
        'description',
    ];

    /**
     * Cast datetime fields
     */
    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function games()
    {
        return $this->hasOne(Categorie::class, 'id', 'games')
            ->select('id', 'name');
    }

    /**
     * Replace finished_at in API response
     */
    public function getFinishedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }

        $end = Carbon::parse($value);

        if ($end->isPast()) {
            return 'Ended';
        }

        $diff = now()->diff($end);

        if ($diff->days > 0) {
            return "⏱ {$diff->days}d {$diff->h}h {$diff->i}m";
        }

        if ($diff->h > 0) {
            return "⏱ {$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m";
    }
}
