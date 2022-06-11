<?php

namespace App\Http\Controllers;

use App\Events\IssueCreated as EventIssueCreated;
use App\Events\IssueUpdated as EventIssueUpdated;
use App\Http\Requests\IssueRequest;
use App\Models\Category;
use App\Models\Executor;
use App\Models\Filters\FilterTrait;
use App\Models\Filters\IssueFilter;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Responses\ResponseGeneral;
use App\Repositories\IssueRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ExecutorRepository;
use App\Validators\IssueValidator;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Auth;

/**
 * @group Issue
 *
 * ###APIs for managing issues
 *
 *
 * Class IssueController
 * @package App\Http\Controllers
 */
class IssueController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var IssueRepository
     */
    protected $issueRepository;

    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ExecutorRepository
     */
    protected $executorRepository;

    /**
     * @var IssueValidator
     */
    protected $issueValidator;

    /**
     * @var int
     */
    protected $issuesPerPage = null;

    /**
     * IssueController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param IssueRepository $issueRepository
     * @param IssueValidator $issueValidator
     * @param AttachmentRepository $attachmentRepository
     * @param UserRepository $userRepository
     * @param CategoryRepository $categoryRepository
     * @param ExecutorRepository $executorRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        IssueRepository $issueRepository,
        IssueValidator $issueValidator,
        AttachmentRepository $attachmentRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        ExecutorRepository $executorRepository
    ) {
        $this->responseStructured = $responseGeneral;
        $this->issueRepository = $issueRepository;
        $this->issueValidator = $issueValidator;
        $this->attachmentRepository = $attachmentRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->executorRepository = $executorRepository;

        $this->issuesPerPage = config('view.count.issues');
    }

    /**
     * List issues
     *
     * ###Get list issues by filters. With pagination.
     *
     * @authenticated
     *
     * @queryParam status           The status of the issue. Value from list - 'pending', 'in progress', 'closed', 'done', 'archive'.
     * @queryParam archiveStatus    The status of issue is 'archive'. Value is empty.
     * @queryParam priority         The priority of the issue. Value from list - 'trivial', 'low', 'major', 'critical'.
     * @queryParam user             The user who created the task. Value is id user.
     * @queryParam from             The due_date from. Value of the following format - yyyy-mm-dd.
     * @queryParam to               The due_date to. Value of the following format - yyyy-mm-dd.
     * @queryParam searchByTitle    The search by title. Value is string.
     * @queryParam sortByAsc        The sort ascending by 'priority', 'status', 'due_date'. Value is string.
     * @queryParam sortByDesc       The sort descending by 'priority', 'status', 'due_date'. Value is string.
     * @queryParam executor         The executer id. Value is integer.
     * @queryParam reporter         The report id. Value is integer.
     *
     * @response {
     *  "status": true,
     *  "entity": "list issues",
     *  "metadata": "list with 'priorities', 'statuses', 'types', 'isAdmin', 'users'"
     * }
     *
     *
     * @param IssueFilter $filters
     * @return array
     */
    public function index(IssueFilter $filters): array
    {
        $filters->request()->flash();
        $this->buildResponseAdditional(['priorities', 'statuses', 'types', 'isAdmin', 'users']);
        $issues = $this->issueRepository->getByFilters($filters, false, $this->issuesPerPage);

        return $this->responseStructured
            ->addEntity($issues)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     *  List issues by floor
     *
     * ###Get list issues by filters and floor. With pagination.
     *
     * @authenticated
     *
     * @queryParam id               required    The id of the floor.
     * @queryParam status                       The status of the issue. Value from list - 'pending', 'in progress', 'closed', 'done', 'archive'.
     * @queryParam archiveStatus                The status of issue is 'archive'. Value is empty.
     * @queryParam priority                     The priority of the issue. Value from list - 'trivial', 'low', 'major', 'critical'.
     * @queryParam user                         The user who created the task. Value is id user.
     * @queryParam from                         The due_date from. Value of the following format - yyyy-mm-dd.
     * @queryParam to                           The due_date to. Value of the following format - yyyy-mm-dd.
     * @queryParam searchByTitle                The search by title. Value is string.
     * @queryParam sortByAsc                    The sort ascending by 'priority', 'status', 'due_date'. Value is string.
     * @queryParam sortByDesc                   The sort descending by 'priority', 'status', 'due_date'. Value is string.
     *
     * @response {
     *  "status": true,
     *  "entity": "list issues",
     *  "metadata": "list with 'priorities', 'statuses', 'types', 'isAdmin', 'users' ,'floor'"
     * }
     *
     * @param IssueFilter $filters
     * @param $id
     * @return array
     */
    public function floor(IssueFilter $filters, $id): array
    {
        $filters->request()->flash();
        $this->buildResponseAdditional(['priorities', 'statuses', 'types', 'isAdmin', 'users']);
        $floor = $this->categoryRepository->getByIdWithoutParent($id);
        $issues = $this->issueRepository->getByFiltersAndCategory($filters, $id, false, $this->issuesPerPage);

        return $this->responseStructured
            ->addEntity($issues)
            ->setStatus(true)
            ->addMetadata($floor, 'floor')
            ->getResponse();
    }

    /**
     * Issue save
     *
     * ###Issue save by request.
     *
     * @authenticated
     *
     * @bodyParam title         string  required    The title of the issue(required;filled;max:255). Example: title
     * @bodyParam category_id   int     required    The room of the office(required;numeric). Example: 1
     * @bodyParam reporter_id   int                 The user is reporter. Example: 1
     * @bodyParam due_date      date    required    The end date of the issue(required;date). Example: 2019-01-01
     * @bodyParam description   string  required    The description of the issue(required;filled). Example: description
     * @bodyParam attachment[]  file                The list attachments of the issue. Example: attachment.jpg
     *
     * @response {
     *  "status": true,
     *  "entity": "new issue",
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
    public function store(Request $request): array
    {
        $this->authorize('create', Issue::class);

        DB::beginTransaction();
        $attachments = null;

        try {
            $issue = $this->issueRepository->create($request->all());
            $attachments = $this->attachmentRepository->createMany($request->file('attachment'), $issue->id);

            DB::commit();
            $this->responseStructured->setStatus(true);

            event(new EventIssueCreated($issue));
        } catch (ValidatorException $e) {
            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (!$this->responseStructured->getStatus()) {
                return $this->rollbackAttachment($attachments);
            }
        }

        $this->buildResponseIssue($issue->id);
        $this->responseStructured->addMessage(trans('issue.success.created'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Rollback attachments
     *
     * @param $attachments
     * @return array
     * @throws \Exception
     */
    private function rollbackAttachment($attachments): array
    {
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $file = public_path($attachment->filename);
                @unlink($file);
            }
        }

        DB::rollback();

        return $this->responseStructured->getResponse();
    }

    /**
     * Issue update
     *
     * ###Issue update by request.
     *
     * @authenticated
     *
     * @bodyParam id            int     required    The id of the issue. Example: 1
     * @bodyParam title         string  required    The title of the issue(required;filled;max:255). Example: title
     * @bodyParam category_id   int     required    The room of the office(required;numeric). Example: 1
     * @bodyParam reporter_id   int                 The user is reporter. Example: 1
     * @bodyParam due_date      date    required    The end date of the issue(required;date). Example: 2019-01-01
     * @bodyParam description   string  required    The description of the issue(required;filled). Example: description
     * @bodyParam attachment[]  file                The list attachments of the issue. Example: attachment.jpg
     * @bodyParam executor_id   int     required    The executor id(required;numeric). Example: 1
     *
     * @response {
     *  "status": true,
     *  "entity": "updated issue",
     *  "metadata": "list with 'priorities', 'statuses', 'types', 'isAdmin', 'users' ,'floor'",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param IssueRequest $request
     * @param Issue $issue
     * @return array
     * @throws \Exception
     */
    public function update(IssueRequest $request, Issue $issue): array
    {
        $this->authorize('update', Issue::class);

        $attachments = null;
        $data = $request->validated();

        try {
            $this->issueRepository->update($data, $issue->id);
            $file = $request->file('attachments');
            $attachments = $this->attachmentRepository->createMany($file, $issue->id);
            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (!$this->responseStructured->getStatus()) {
                return $this->rollbackAttachment($attachments);
            }
        }
        $this->buildResponseIssue($issue->id);
        $this->responseStructured->addMessage(trans('issue.success.updated'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Single field update in issue
     *
     * ###Save any fields to issue. Required only 1 field - id.
     *
     * @authenticated
     *
     * @bodyParam id            int     required    The id of the issue. Example: 1
     * @bodyParam status        string              The status of the issue(sometimes). Value from list - 'pending', 'in progress', 'closed', 'done', 'archive'. Example: pending
     * @bodyParam priority      string              The priority of the issue(sometimes). Value from list - 'trivial', 'low', 'major', 'critical'. Example: trivial
     * @bodyParam title         string              The title of the issue(sometimes;filled;max:255). Example: title
     * @bodyParam category_id   int                 The room of the office(sometimes;numeric). Example: 1
     * @bodyParam due_date      date                The end date of the issue(sometimes;date). Example: 2019-01-01
     * @bodyParam description   string              The description of the issue(sometimes;filled). Example: description
     * @bodyParam attachment[]  file                The list attachments of the issue. Example: attachment.jpg
     * @bodyParam executor_id   int                 The executor id. Example: 1
     *
     * @response {
     *  "status": true,
     *  "entity": "updated issue",
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
    public function singleFieldUpdate(Request $request): array
    {
        try {
            $issue = $this->issueRepository->patch($request->all());

            if (!$issue) {
                $this->responseStructured->addMessage(trans('issue.fail'), 'errors');
                return $this->responseStructured->getResponse();
            }

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

        $this->buildResponseIssue($issue->id);
        $this->responseStructured->addMessage(trans('issue.success.updated'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * List issues archived
     *
     * ###Get list issues by filters and with status 'archive'. With pagination.
     *
     * @authenticated
     *
     * @queryParam status           The status of the issue. Value from list - 'pending', 'in progress', 'closed', 'done', 'archive'.
     * @queryParam archiveStatus    The status of issue is 'archive'. Value is empty.
     * @queryParam priority         The priority of the issue. Value from list - 'trivial', 'low', 'major', 'critical'.
     * @queryParam user             The user who created the task. Value is id user.
     * @queryParam from             The due_date from. Value of the following format - yyyy-mm-dd.
     * @queryParam to               The due_date to. Value of the following format - yyyy-mm-dd.
     * @queryParam searchByTitle    The search by title. Value is string.
     * @queryParam sortByAsc        The sort ascending by 'priority', 'status', 'due_date'. Value is string.
     * @queryParam sortByDesc       The sort descending by 'priority', 'status', 'due_date'. Value is string.
     *
     * @response {
     *  "status": true,
     *  "entity": "list issues",
     *  "metadata": "list with 'priorities', 'statuses', 'types', 'isAdmin', 'users' ,'floor'"
     * }
     *
     *
     * @param IssueFilter $filters
     * @return array
     */
    public function archive(IssueFilter $filters): array
    {
        $archive = $this->issueRepository->getArchivedByFilters($filters, $this->issuesPerPage);
        $this->responseStructured->addEntity($archive);
        $this->buildResponseAdditional(['priorities', 'users']);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * @param Request $request
     * @param  $id
     *
     * @return array
     * @throws \Exception
     */
    public function archiveSend(Request $request, $id): array
    {

        $issue = $this->issueRepository->archivedUpdateId($id);
        $this->responseStructured->setStatus(true);

        $this->buildResponseIssue($issue->id);
        $this->responseStructured->addMessage(trans('issue.success.archive'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Issue preview
     *
     * ###Get issue by id.
     *
     * @authenticated
     *
     * @queryParam id   required    The id of the issue.
     *
     * @response {
     *  "status": true,
     *  "entity": "issue",
     *  "metadata": "list with 'categories', 'executors', 'priorities', 'statuses', 'types', 'isAdmin'"
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
    public function edit($id): array
    {
        $this->authorize('list', Issue::class);
        $this->buildResponseIssue($id);

        $this->buildResponseAdditional(['categories', 'executors', 'priorities', 'statuses', 'types', 'isAdmin']);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    protected function buildResponseIssue($id)
    {
        $issue = $this->issueRepository->getByIdWithCategory($id);

        $issue->loadMissing('attachments');
        $this->responseStructured->addEntity($issue);
    }

    /**
     * @param array $include
     */
    protected function buildResponseAdditional($include = [])
    {
        foreach ($include as $item) {
            switch ($item) {
                case 'categories' :
                    $categories = Category::get(['name','id']);
                    $this->responseStructured->addMetadata($categories, 'categories');
                    break;

                case 'executors' :
                    $executors = Executor::get();
                    $this->responseStructured->addMetadata($executors, 'executors');
                    break;

                case 'priorities' :
                    $priorities = Issue::priorities();
                    $this->responseStructured->addMetadata($priorities, 'priorities');
                    break;

                case 'statuses' :
                    $statuses = Issue::statuses();
                    $this->responseStructured->addMetadata($statuses, 'statuses');
                    break;

                case 'types' :
                    $types = Issue::types();
                    $this->responseStructured->addMetadata($types, 'types');
                    break;

                case 'isAdmin' :
                    $isAdmin = Auth::user()->isAdministrator();
                    $this->responseStructured->addMetadata($isAdmin, 'isAdmin');
                    break;

                case 'users' :
                    $users = User::get(['name', 'id']);
                    $this->responseStructured->addMetadata($users, 'users');
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Data for create new issue
     *
     * ###Get data for view form to create new issue.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "metadata": "list with 'categories', 'executors', 'priorities', 'statuses', 'types', 'isAdmin'"
     * }
     *
     *
     * @return array
     */
    public function create(): array
    {
        $defaultExecutorId = $this->executorRepository->getDefaultExecutorId();
        $this->buildResponseAdditional(['categories', 'executors', 'priorities', 'statuses', 'types', 'isAdmin']);
        $this->responseStructured->addMetadata($defaultExecutorId, 'defaultExecutorId');
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Issue delete
     *
     * ###Issue delete by id.
     *
     * @authenticated
     *
     * @queryParam id   required    The id of the issue.
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
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function remove($id): array
    {
        $issue = Issue::findOrFail($id);

        $this->authorize('delete', $issue);

        $userId = $issue->getIssueUserIdAttribute();

        if ($userId == Auth::id() || Auth::user()->isAdministrator()) {
            $issue->delete();

            return $this->responseStructured
                ->addMessage(trans('issue.success.remove'), 'success')
                ->setStatus(true)
                ->getResponse();
        }

        return $this->responseStructured->getResponse();
    }

    /**
     * @param int $id
     * @return int
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeImage(int $id)
    {
        $issue = $this->issueRepository->find($id);

        return $this->issueRepository->removeIssueImage($issue);
    }
}
