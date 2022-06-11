<?php

namespace App\Http\Controllers;

use App\Events\UserRead;
use App\Http\Responses\ResponseGeneral;
use App\Events\PostRead;
use App\Events\IssueRead;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Repositories\IssueRepository;
use App\Repositories\PostRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * @group Common
 *
 * ###APIs for managing common request
 *
 *
 * Class CommonController
 * @package App\Http\Controllers
 */
class CommonController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var IssueRepository
     */
    protected $issueRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var int
     */
    protected $notificationsPerPage = null;

    /**
     * @var int
     */
    protected $notificationsPerPageFromRequest = null;

    /**
     * CommonController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param IssueRepository $issueRepository
     * @param PostRepository $postRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        IssueRepository $issueRepository,
        PostRepository $postRepository,
        RoleRepository $roleRepository
    ) {
        $this->responseStructured = $responseGeneral;
        $this->issueRepository = $issueRepository;
        $this->postRepository = $postRepository;
        $this->roleRepository = $roleRepository;

        $this->notificationsPerPage = config('view.count.notifications');
    }

    /**
     * List notifications
     *
     * ###Get list notifications by authorized user. With pagination,
     * and the ability to select the number of notifications per page.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "list notifications"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @return array
     */
    public function notifications(Request $request): array
    {
        if (request()->get('per')) {
            $this->notificationsPerPageFromRequest = request()->get('per');
        }

        $notificationsData = $this->getNotificationsData();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addEntity($notificationsData);

        return $this->responseStructured->getResponse();
    }

    /**
     * @return mixed
     */
    protected function getNotificationsData()
    {
        $data = [];
        $user = Auth::user();

        $notifications = $user->unreadNotifications()
            ->paginate($this->notificationsPerPageFromRequest ?? $this->notificationsPerPage);

        foreach ($notifications as $key => $notification) {

            if (array_key_exists('post_id', $notification->data)) {

                $postId = $notification->data['post_id'];

                try {
                    $post = $this->postRepository->getForNotificationById($postId);
                    $data[$key] = $post;
                } catch (\Exception $e) {

                    event(new PostRead($postId));
                }
            } elseif (array_key_exists('issue_id', $notification->data)) {

                $issueId = $notification->data['issue_id'];

                try {
                    $issue = $this->issueRepository->getForNotificationById($issueId);
                    $data[$key] = $issue;

                } catch (\Exception $e) {

                    event(new IssueRead($issueId));
                }
            } elseif (array_key_exists('role_id', $notification->data)) {

                $role_id = $notification->data['role_id'];
                $user_id = $notification->data['user_id'] ?? 0;

                try {
                    $role = $this->roleRepository->getRoleById($role_id);
                    $userData = [
                        "type" => "role",
                        "user_id" => $user_id,
                        "user" => [
                            "id" => $user->id,
                            "name" => $user->name
                        ]
                    ];

                    $data[$key] = array_merge($role->toArray(), $userData);

                } catch (\Exception $e) {

                    event(new UserRead($role_id));
                }
            }
        }

        $notifications->splice(0, count($notifications->items()), $data);

        return $notifications;
    }
}
