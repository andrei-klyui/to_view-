<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AttachmentRepository.
 *
 * @package namespace App\Repositories;
 */
interface AttachmentRepository extends RepositoryInterface
{
    /**
     * @param \Illuminate\Http\UploadedFile[] $files
     * @param int $issueId
     * @return array|mixed
     */
    public function createMany($files, int $issueId);
}
