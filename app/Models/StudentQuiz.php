<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuiz extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function scopeIsTaken($query, $student_id, $quiz_id)
    {
        return $query->where('quiz_id', $quiz_id)
            ->where('student_id', $student_id);
    }

    public function scopeRelations($query)
    {
        return $query->with(['student', 'quiz', 'question', 'answer']);
    }
}
