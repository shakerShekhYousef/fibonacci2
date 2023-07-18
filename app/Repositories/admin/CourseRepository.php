<?php

namespace App\Repositories\admin;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Course;
use App\Models\Video;
use App\Repositories\BaseRepository;
use Exception;

class CourseRepository extends BaseRepository
{
    public function model()
    {
        return Course::class;
    }

    public function setFreeTrail($id, $end_free_trail)
    {
        try {
            $course = $this->model->find($id);
            if ($course == null) {
                throw new NotFoundException('course not found');
            }
            $course->end_free_trail = $end_free_trail;
            $course->save();
            Video::where('course_id', $id)->update(['end_free_trail' => $end_free_trail]);

            return true;
        } catch (Exception $e) {
            throw new GeneralException('server error');
        }
    }
}
