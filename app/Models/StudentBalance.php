<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBalance extends Model
{
    use HasFactory;

    protected $guarded;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
