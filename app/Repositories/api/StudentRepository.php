<?php

namespace App\Repositories\api;

use App\Exceptions\GeneralException;
use App\Models\Student;
use App\Models\StudentBalance;
use App\Models\StudentVideo;
use App\Models\Video;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class StudentRepository extends BaseRepository
{
    public function model()
    {
        return Student::class;
    }

    public function save_video_progress(array $data)
    {
        return DB::transaction(function () use ($data) {
            //save video progress
            $progress = StudentVideo::create([
                'video_id' => $data['video_id'],
                'student_id' => auth()->user()->id,
                'stopped_at' => $data['stopped_at']
            ]);

            return $progress;
        });
        throw new GeneralException('error');
    }

    public function buy_video(array $data)
    {
        return DB::transaction(function () use ($data) {
            //Get student id
            $student_id = auth()->id();
            //Get video price
            $video_id = $data['video_id'];
            $video_price = Video::query()->where('id', $video_id)->pluck('price')->first();
            //Check if video already buy
            $already = StudentVideo::query()->where([
                ['video_id',$video_id],
                ['student_id',$student_id]
                ])->first();
            if ($already){
                return forbidden_response('You are already subscribe this video.');
            }
            //Check balance
            $check_balance = $this->check_balance($student_id, $video_price);
            if ($check_balance !== true) {
                return $check_balance;
            }else{
                $buy_video = StudentVideo::create([
                    'video_id' => $video_id,
                    'student_id' => $student_id,
                    'stopped_at' => 0,
                ]);
                $this->update_balance($student_id,$video_price);
                return success_response($buy_video);
            }
        });
        throw new GeneralException('error');
    }

    public function check_balance($student_id, $video_price)
    {
        //Get student balance
        $balance = StudentBalance::query()->where('student_id', $student_id)
            ->pluck('balance')
            ->first();
        //Check if student balance > 0
        if ($balance <= 0) {
            return forbidden_response('Your balance is 0, please charge the balance first.');
            //Check if video price > student balance
        } elseif ($balance < $video_price) {
            return forbidden_response('You do not have enough balance.');
        }else{
            return true;
        }

    }

    public function update_balance($student_id,$video_price){
        //Get student balance
        $student_balance = StudentBalance::query()->where('student_id', $student_id)
            ->first();
        //Update balance
        $new_balance = $student_balance->balance - $video_price;
        $student_balance->update([
            'balance'=>$new_balance
        ]);
    }

}
