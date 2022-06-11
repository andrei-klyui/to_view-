<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Models\Category;
use App\Models\Office;
use App\Models\Filters\CategoryFilter;
use App\Repositories\CategoryRepository;
use App\Validators\CategoryValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;

class CategoryController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CategoryValidator
     */
    protected $categoryValidator;

    /**
     * @var int
     */
    protected $categoriesPerPage = null;

    /**
     * CategoryController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param CategoryRepository $categoryRepository
     * @param CategoryValidator $categoryValidator
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        CategoryRepository $categoryRepository,
        CategoryValidator $categoryValidator
    ) {
        $this->responseStructured = $responseGeneral;
        $this->categoryRepository = $categoryRepository;
        $this->categoryValidator = $categoryValidator;

        $this->categoriesPerPage = config('view.count.categories');
    }

    /**
     * * * List categories
     *
     * ###Get list categories. With pagination.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "list categories"
     * }
     *
     * @return array
     * @throws AuthorizationException
     */
    public function index(CategoryFilter $filters): array
    {
        $this->authorize('create', Category::class);
        $categories = $this->categoryRepository->getList($filters);

        return $this->responseStructured
            ->addEntity($categories)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * @return array
     * @throws AuthorizationException
     */
    public function create(): array
    {
        $this->authorize('create', Category::class);

        return $this->responseStructured
            ->setStatus(true)
            ->addMetadata(Office::all(['id', 'name']), 'offices')
            ->getResponse();
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthorizationException|ValidatorException
     */
    public function store(Request $request): array
    {
        $this->authorize('create', Category::class);

        $this->categoryValidator->with($request->all())->passesOrFail(CategoryValidator::RULE_CREATE_NEW_CATEGORY);
        $category = $this->categoryRepository->create($request->all());

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($category)
            ->addMessage(trans('category.success.created'), 'success')
            ->getResponse();
    }

    /**
     * @param $id
     * @return array
     */
    public function show($id): array
    {
        $category = $this->categoryRepository->find($id);

        return $this->responseStructured
            ->addEntity($category)
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
        $this->authorize('update', Category::class);

        $this->categoryValidator->with($request->all())->passesOrFail(CategoryValidator::RULE_UPDATE_CATEGORY);
        $this->categoryRepository->update($request->all(), (int)$id);
        $category = $this->categoryRepository->find($id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($category)
            ->addMessage(trans('category.success.updated'), 'success')
            ->getResponse();
    }

    /**
     * @param $id
     * @return array
     * @throws AuthorizationException
     */
    public function destroy($id): array
    {
        $office = $this->categoryRepository->find($id);

        $this->authorize('delete', $office);

        $office->delete();

        return $this->responseStructured
            ->setStatus(true)
            ->addMessage(trans('category.success.delete'), 'success')
            ->getResponse();
    }

    /**
     * @param $id
     * @return array
     */
    public function edit($id): array
    {
        $category = $this->categoryRepository->find($id);

        return $this->responseStructured
            ->addMetadata(Office::all(['id', 'name']), 'offices')
            ->addEntity($category)
            ->setStatus(true)
            ->getResponse();
    }
}
