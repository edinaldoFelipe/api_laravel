<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_name',
        'task_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
