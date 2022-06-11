<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Attachment;
use App\Validators\AttachmentValidator;

/**
 * Class AttachmentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AttachmentRepositoryEloquent extends BaseRepository implements AttachmentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Attachment::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AttachmentValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param \Illuminate\Http\UploadedFile[] $files
     * @param int $issueId
     * @return array|mixed
     */
    public function createMany($files, int $issueId)
    {
        $attachments = [];

        if (!$files) {
            return $attachments;
        }

        foreach ($files as $file) {
            try {
                $path = \Storage::disk(env('FILESYSTEM_DRIVER'))->putFile('public/uploads/attachments', $file);

                $attachmentData = [
                    'title' => $file->getClientOriginalName(),
                    'filename' => $path,
                    'file_url' => \Storage::url($path),
                    'type' => $file->extension(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'issue_id' => $issueId
                ];

                $attachments[] = $this->create($attachmentData);
            } catch (\Exception $e) {

                \Storage::delete($path);
            }
        }

        return $attachments;
    }
}
