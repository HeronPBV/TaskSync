<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_id',
        'name',
        'position',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($column) {
            if (!$column->position) {
                $lastPosition = $column->board->columns()->max('position');
                $column->position = is_null($lastPosition) ? 1 : $lastPosition + 1;
            }
        });
    }


    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }
}
