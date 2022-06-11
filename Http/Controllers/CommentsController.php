<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Filters\FilterTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Post;
use App\Http\Responses\ResponseGeneral;
use Illuminate\Database\Eloquent\Model;
use App\Validators\CommentValidator;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\CommentRepository;

/**
 * @group Comment
 *
 * ###APIs for managing comments
 *
 *
 * Class CommentsController
 * @package App\Http\Controllers
 */
class CommentsController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * @var CommentValidator
     */
    protected $commentValidator;

    /**
     * CommentsController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param CommentRepository $commentRepository
     * @param CommentValidator $commentValidator
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        CommentRepository $commentRepository,
        CommentValidator $commentValidator
    ) {
        $this->responseStructured = $responseGeneral;
        $this->commentRepository = $commentRepository;
        $this->commentValidator = $commentValidator;
    }

    /**
     * Add comment to issue
     *
     * ###Add comment to issue.
     *
     * @authenticated
     *
     * @queryParam  id          required                The id of the issue.
     * @bodyParam   content     string      required    The content of the comment(required;filled). Example: content
     *
     * @response {
     *  "status": true,
     *  "entity": "list comments with first new comment",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @param $id
     * @return array
     * @throws AuthorizationException|ValidatorException
     */
    public function addCommentToIssue(Request $request, $id): array
    {
        $this->authorize('create', Issue::class);

        /** @var Issue $issue */
        $issue = Issue::findOrFail($id);

        $result = $this->addComment($request, $issue);
        if (!($result instanceof Comment)) {
            return $result;
        }

        return $this->getByIssue($id);
    }

    /**
     * Add comment to post
     *
     * ###Add comment to post.
     *
     * @authenticated
     *
     * @queryParam  id          required                The id of the issue.
     * @bodyParam   content     string      required    The content of the comment(required;filled). Example: content
     *
     * @response {
     *  "status": true,
     *  "entity": "list comments with first new comment",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @param $id
     * @return array
     * @throws AuthorizationException|ValidatorException
     */
    public function addCommentToPost(Request $request, $id): array
    {
        $this->authorize('create', Post::class);

        /** @var Post $post */
        $post = Post::findOrFail($id);

        $result = $this->addComment($request, $post);
        if (!($result instanceof Comment)) {
            return $result;
        }

        return $this->getByPost($id);
    }

    /**
     * @param Request $request
     * @param Model $model
     * @return mixed
     * @throws ValidatorException
     */
    protected function addComment(Request $request, Model $model)
    {
        $data = $request->all();
        $data['user_id'] = \Auth::id();

        $this->commentValidator->with($request->all())->passesOrFail();

        $comment = $model->comments()->create($data);

        if ($comment) {
            $comment->loadMissing('user:id,name,avatar');
        }

        $this->responseStructured->addMessage(trans('comment.comment-success'), 'success');
        $this->responseStructured->setStatus(true);

        return $comment;
    }

    /**
     * Get list comments by issue
     *
     * ###Get list comments by issue id. With pagination.
     *
     * @authenticated
     *
     * @queryParam  id  required    The id of the issue.
     *
     * @response {
     *  "status": true,
     *  "entity": "list comments"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param $id
     * @return array
     * @throws AuthorizationException
     */
    public function getByIssue($id): array
    {
        $this->authorize('list', Issue::class);

        $limit = config('view.count.comments.issue');
        $comments = $this->commentRepository->getByEntityId(Issue::class, $id, $limit);

        $this->responseStructured->addEntity($comments);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Get list comments by post
     *
     * ###Get list comments by post id. With pagination.
     *
     * @authenticated
     *
     * @queryParam  id  required    The id of the post.
     *
     * @response {
     *  "status": true,
     *  "entity": "list comments"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param $id
     * @return array
     * @throws AuthorizationException
     */
    public function getByPost($id): array
    {
        $this->authorize('list', Post::class);
        $limit = config('view.count.comments.news');
        $comments = $this->commentRepository->getByEntityId(Post::class, $id, $limit);

        $this->responseStructured->addEntity($comments);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * @param Request $request
     * @param Comment $comment
     *
     * @return array
     * @throws ValidatorException
     * @throws AuthorizationException
     */
    public function update(Request $request, Comment $comment): array
    {
        $data = $request->all();

        $this->commentValidator->with($data)->passesOrFail();
        $this->authorize('update', $comment);
        $comment->update($data);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove comment
     *
     * ###Remove comment
     *
     * @authenticated
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
     * @return array
     * @throws AuthorizationException
     */
    public function destroy($id): array
    {
        $comment = $this->commentRepository->find($id);
        $this->authorize('delete', $comment);

        $this->commentRepository->delete($id);

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('comment.success.delete'), 'success');

        return $this->responseStructured->getResponse();
    }
}
