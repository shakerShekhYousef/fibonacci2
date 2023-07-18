<?php

namespace App\Http\Resources\api;

use App\Models\StudentVideo;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    //Check subscribed
    public function subscribed($video_id){
        $video = StudentVideo::query()->where([
            ['student_id',auth()->id()],
            ['video_id',$this->id]
        ])->first();
        if ($video){
            return true;
        }else{
            return false;
        }
    }
    //Resource
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'file' => $this->file,
            'price' => $this->price,
            'course_id' => $this->course_id,
            'subscribed' =>$this->subscribed($this->id)
        ];
    }
}
