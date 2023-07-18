<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(StudentQuiz::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
