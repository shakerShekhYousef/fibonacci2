<?php

namespace App\Repositories\admin;

use App\Models\Subject;
use App\Repositories\BaseRepository;

class SubjectRepository extends BaseRepository
{
    public function model()
    {
        return Subject::class;
    }
}
