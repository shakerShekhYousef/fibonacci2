<?php

namespace App\Http\Resources\api;

use App\Models\Student;
use App\Models\Video;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseVideoProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'stopped_at' => $this->stopped_at,
            'student' => Student::find($this->student_id),
            'video' => Video::find($this->video_id),
        ];
    }
}
