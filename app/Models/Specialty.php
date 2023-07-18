<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function courses()
    {
        return $this->hasMany(CourseSpecialty::class);
    }

    public function teachers()
    {
        return $this->hasMany(TeacherSpecialty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
