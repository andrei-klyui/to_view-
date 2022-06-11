<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Issue;
use App\Repositories\AttachmentRepository;
use App\Http\Responses\ResponseGeneral;
use Illuminate\Support\Facades\Auth;

/**
 * @group Attachment
 *
 * ###APIs for managing attachments
 *
 *
 * Class AttachmentController
 * @package App\Http\Controllers
 */
class AttachmentController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseGeneral;

    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * AttachmentController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param AttachmentRepository $attachmentRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        AttachmentRepository $attachmentRepository
    ) {
        $this->attachmentRepository = $attachmentRepository;
        $this->responseGeneral = $responseGeneral;
    }

    /**
     * Attachment delete
     *
     * ###Attachment delete by id.
     *
     * @authenticated
     *
     * @queryParam id   required    The id of the attachment.
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param int $id
     * @return mixed
     */
    public function remove($id)
    {
        $issueId = Attachment::findOrFail($id)->getImageIssueIdAttribute();
        $userIssue = Issue::findOrFail($issueId);

        $userId = $userIssue->getIssueUserIdAttribute();

        if ($userId == Auth::id() || Auth::user()->isAdministrator()) {
            $this->attachmentRepository->delete($id);
            $this->responseGeneral->addMessage(trans('attachment.success.remove'), 'success');
            $this->responseGeneral->setStatus(true);

            return $this->responseGeneral->getResponse();
        }

        $this->responseGeneral->addMessage(trans('attachment.fail.exist'), 'errors');

        return $this->responseGeneral->getResponse();
    }
}
