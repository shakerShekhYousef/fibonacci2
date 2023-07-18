<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(StudentVideo::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function quiz()
    {
        return $this->belongsTo(StudentQuiz::class);
    }

    public function balance()
    {
        return $this->hasOne(StudentBalance::class);
    }
}
