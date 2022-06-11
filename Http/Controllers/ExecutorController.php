<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Models\Executor;
use App\Models\Filters\FilterTrait;
use App\Models\Filters\ExecutorFilter;
use App\Repositories\ExecutorRepository;
use App\Validators\ExecutorValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;

class ExecutorController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var ExecutorRepository
     */
    protected $executorRepository;

    /**
     * @var ExecutorValidator
     */
    protected $executorValidator;

    /**
     * @var int
     */
    protected $executorsPerPage = null;

    /**
     * CategoryController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param ExecutorRepository $executorRepository
     * @param ExecutorValidator $executorValidator
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        ExecutorRepository $executorRepository,
        ExecutorValidator $executorValidator
    ) {
        $this->responseStructured = $responseGeneral;
        $this->executorRepository = $executorRepository;
        $this->executorValidator = $executorValidator;

        $this->executorsPerPage = config('view.count.executors');
    }

    /**
     * List executors
     *
     * ###Get list executors. With pagination.
     *
     * @param ExecutorFilter $filters
     * @return array
     * @throws AuthorizationException
     */
    public function index(ExecutorFilter $filters): array
    {
        $this->authorize('list', Executor::class);

        $executors = $this->executorRepository->getList($filters);

        return $this->responseStructured
            ->addEntity($executors)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * Create executor
     *
     * ###Get data for create executor.
     *
     * @return array
     * @throws AuthorizationException
     */
    public function create(): array
    {
        $this->authorize('create', Executor::class);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Executor save
     *
     * ###Executor save by request.
     *
     * @authenticated
     *
     * @bodyParam name         string  required    The title of the menu(required;filled;max:191). Example: test
     * @bodyParam email                 email   required    The email of the user(string;max:255;email;unique:users). Example: email@email.com
     * @bodyParam description           string  required    The slug of the menu(required;). Example: user/create
     *
     * @response {
     *  "status": true,
     *  "entity": "new executor",
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
        $this->authorize('create', Executor::class);

        $executorCreate = $this->executorRepository->create($request->all());
        $executor = $this->executorRepository->find($executorCreate->id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($executor)
            ->addMessage(trans('executor.success.created'), 'success')
            ->getResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return array
     */
    public function edit(int $id): array
    {
        $executor = $this->executorRepository->find($id);

        return $this->responseStructured
            ->addEntity($executor)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @throws AuthorizationException|ValidatorException
     */
    public function update(Request $request, $id): array
    {
        $this->authorize('update', Executor::class);

        $this->executorValidator->with($request->all())->passesOrFail(ExecutorValidator::RULE_UPDATE_EXECUTOR);
        $this->executorRepository->update($request->all(), (int)$id);
        $executor = $this->executorRepository->find($id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($executor)
            ->addMessage(trans('executor.success.updated'), 'success')
            ->getResponse();
    }
}
