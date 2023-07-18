<?php

namespace App\Repositories\api;

use App\Exceptions\GeneralException;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\StudentQuiz;
use App\Models\Video;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class QuizRepository extends BaseRepository
{
    public function model()
    {
        return Quiz::class;
    }

    /**
     * get quizes in video
     *
     * @param  int  $video_id
     * @return mixed
     */
    public function videoQuiz($video_id)
    {
        $quiz = $this->model->with(['questions'])->where('video_id', $video_id)->first();
        if ($quiz) {
            $data = $quiz->toArray();
            $student = Student::where('user_id', auth()->user()->id)->first();

            if ($student) {
                $check = StudentQuiz::where('student_id', $student->id)
                    ->where('quiz_id', $data['id'])
                    ->first();
                $data['is_taken'] = $check ? true : false;

                return $data;
            }
        }

        return $quiz;
    }

    public function create(array $data)
    {
        $check = Quiz::where('video_id', $data['video_id'])->first();
        if ($check) {
            return $check;
        }

        return DB::transaction(function () use ($data) {
            //add quiz to video
            $quiz = parent::create([
                'title' => $data['title'],
                'video_id' => $data['video_id'],
            ]);

            return $quiz;
        });
        throw new GeneralException('error');
    }

    public function update(Quiz $quiz, array $data)
    {
        return DB::transaction(function () use ($quiz, $data) {
            if ($quiz->update([
                'title' => $data['title'] !== null ? $data['title'] : $quiz->title,
                'video_id' => $data['video_id'] !== null ? $data['video_id'] : $quiz->video_id,
            ])) {
                return $quiz;
            }
        });

        throw new GeneralException('error');
    }
}
