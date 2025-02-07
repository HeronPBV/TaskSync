<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'column_id',
        'title',
        'description',
        'due_date',
        'priority',
        'position',
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }
}
