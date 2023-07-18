<?php

namespace App\Trait;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    /**
     * upload file
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @return string
     */
    public function upload(UploadedFile $file, $path)
    {
        $fileName = time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($path, $fileName, 'public');

        return $path;
    }

    /**
     * delete file
     *
     * @param  string  $path
     */
    public function delete_file($path)
    {
        try {
            Storage::disk('public')->delete($path);
        } catch (Exception $e) {
        }
    }
}
