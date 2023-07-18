<?php

namespace App\Repositories\admin;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Http\Requests\admin\Videos\UpdateVideoRequest;
use App\Models\Video;
use App\Repositories\BaseRepository;
use App\Trait\FileTrait;
use Exception;
use Illuminate\Support\Facades\Storage;

class VideoRepository extends BaseRepository
{
    use FileTrait;

    public function model()
    {
        return Video::class;
    }

    public function videos($course_id)
    {
        $videos = Video::where('course_id', $course_id)->get();

        return $videos;
    }

    public function create(array $request)
    {
        $video = Video::create($request);

        return $video;
    }

    /**
     * update video
     *
     * @param  int  $id
     * @param  UpdateVideoRequest  $data
     * @return mixed
     */
    public function update($id, UpdateVideoRequest $request)
    {
        try {
            $video = $this->model->find($id);
            if ($video == null) {
                throw new NotFoundException('video not found');
            }
            $data = [
                'title' => $request['title'],
                'description' => $request['description'],
                'price' => $request['price'],
            ];
            if ($request['end_free_trail'] != null) {
                $data['end_free_trail'] = $request['end_free_trail'];
            }
            $video->update($data);
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('videos', $fileName, 'public');
                if ($video->file != null) {
                    Storage::disk('public')->delete($video->file);
                }
                $video->file = $path;
                $video->save();
            }

            return $video;
        } catch (Exception $e) {
            throw new GeneralException('server error');
        }
    }

    /**
     * delete video
     *
     * @param  int  $id
     * @return bool
     */
    public function destroy($id)
    {
        $video = $this->model->find($id);
        if ($video != null) {
            if ($video->file != null) {
                $this->delete_file($video->file);
            }
            $video->delete();
        }
    }
}
