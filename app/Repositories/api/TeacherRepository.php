<?php

namespace App\Repositories\api;

use App\Models\Teacher;
use App\Repositories\BaseRepository;

class TeacherRepository extends BaseRepository
{
    public function model()
    {
        return Teacher::class;
    }
}
