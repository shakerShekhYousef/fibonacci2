<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSpecialty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
