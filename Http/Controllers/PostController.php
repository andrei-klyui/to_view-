<?php

namespace App\Http\Controllers;

use App\Models\Filters\FilterTrait;
use App\Models\Filters\PostFilter;
use App\Repositories\PostRepository;
use App\Http\Responses\ResponseGeneral;
use App\Events\PostRead;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Events\PostCreated as EventPostCreated;

/**
 * @group Post
 *
 * ###APIs for managing posts
 *
 * Class PostController
 * @package App\Http\Controllers
 */
class PostController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var int
     */
    protected $newsPerPage = null;

    /**
     * @var int
     */
    protected $newsLatest = null;

    /**
     * @var int
     */
    protected $postsPerPage = null;

    /**
     * @var int
     */
    protected $lengthDescriptionShort = null;

    /**
     * PostController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param PostRepository $postRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        PostRepository $postRepository
    ) {
        $this->responseStructured = $responseGeneral;
        $this->postRepository = $postRepository;

        $this->newsPerPage = config('view.count.news.page');
        $this->newsLatest = config('view.count.news.latest');
        $this->postsPerPage = config('view.count.posts.page');
        $this->lengthDescriptionShort = config('view.length.post.description_short');
    }

    /**
     * News preview
     *
     * ###Get post by id.
     *
     * @authenticated
     *
     * @queryParam id   required    The id of the post.
     *
     * @response {
     *  "status": true,
     *  "entity": "post",
     *  "metadata": "list with 'latestPosts'"
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
     * @throws \Exception
     */
    public function single($id): array
    {
        /** @var Post $post */
        $post = $this->postRepository->getByIdWithRelations($id);

        $this->responseStructured->addEntity($post);

        $latestPosts = $this->postRepository->getLatestWithRelations($this->newsLatest);

        $this->responseStructured->addMetadata($latestPosts, 'latestPosts');
        $this->responseStructured->setStatus(true);

        event(new PostRead($post->id));

        return $this->responseStructured->getResponse();
    }

    /**
     * List posts
     *
     * ###Get list posts. With pagination.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "list posts"
     * }
     *
     *
     * @return array
     * @throws AuthorizationException
     */
    public function index(PostFilter $filters)
    {
        $this->authorize('list', Post::class);

        $posts = $this->postRepository->getList($filters);

        return $this->responseStructured
            ->addEntity($posts)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * Create post
     *
     * ###Get data for create post.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     * }
     *
     * @return array
     * @throws AuthorizationException
     */
    public function create(): array
    {
        $this->authorize('create', Post::class);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Post save
     *
     * ###Post save by request.
     *
     * @authenticated
     *
     * @bodyParam title         string  required    The title of the post(required;filled;max:191). Example: title
     * @bodyParam description   string  required    The description of the post(required;filled). Example: description
     * @bodyParam cover         file                The cover of the post. Example: cover.jpg
     *
     * @response {
     *  "status": true,
     *  "entity": "new post",
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
     * @return array
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        try {
            $postNew = $this->postRepository->createWithCover($request->all());

            event(new EventPostCreated($postNew));

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {

            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {

            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {

            if (!$this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }

        $post = $this->postRepository->getByIdWithRelations($postNew->id);
        $this->responseStructured->addEntity($post);
        $this->responseStructured->addMessage(trans('post.success.created'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Edit post
     *
     * ###Get data for edit post.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "post data",
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     * @param int $id
     * @return array
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function edit(int $id): array
    {
        /** @var Post $post */
        $post = $this->postRepository->getByIdWithRelations($id);

        $this->authorize('update', $post);

        $this->responseStructured->addEntity($post);
        $this->responseStructured->setStatus(true);

        event(new PostRead($post->id));

        return $this->responseStructured->getResponse();
    }

    /**
     * Post update
     *
     * ###Post update by request.
     *
     * @authenticated
     *
     * @bodyParam title         string  required    The title of the post(required;filled;max:191). Example: title
     * @bodyParam description   string  required    The description of the post(required;filled). Example: description
     * @bodyParam cover         file                The cover of the post. Example: cover.jpg
     * @bodyParam _method               string   required   The _method of the request - put. Example: put
     *
     * @response {
     *  "status": true,
     *  "entity": "updated post",
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
     * @param int $id
     * @return array
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function update(Request $request, int $id): array
    {
        /** @var Post $post */
        $post = $this->postRepository->getByIdWithRelations($id);

        $this->authorize('update', $post);

        try {
            $input = $request->except(['_method', 'user_id']);

            $this->postRepository->updateByPost($post, $input);

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {

            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {

            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {

            if (!$this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }

        $this->responseStructured->addEntity($post);
        $this->responseStructured->addMessage(trans('post.success.updated'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove post
     *
     * ###Remove post
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
     * @throws \Exception
     */
    public function destroy(int $id): array
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        $this->authorize('delete', $post);

        $post->delete();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('post.success.delete'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove post cover
     *
     * ###Remove post cover
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
    public function removeCover(int $id): array
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        $this->authorize('removeCover', $post);

        if (empty($post->cover)) {
            $this->responseStructured->addMessage(trans('post.remove-file.not-exist'), 'errors');

            return $this->responseStructured->getResponse();
        }

        try {
            $this->postRepository->removePostCover($post);

            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }

        $this->responseStructured->addMessage(trans('post.remove-file.success'), 'success');

        return $this->responseStructured->getResponse();
    }
}
