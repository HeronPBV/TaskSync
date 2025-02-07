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

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }
}
