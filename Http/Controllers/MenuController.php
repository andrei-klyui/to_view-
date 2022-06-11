<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Models\Menu;
use App\Repositories\MenuRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

/**
 * Class MenuController.
 *
 * @package namespace App\Http\Controllers;
 */
class MenuController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var menuRepository
     */
    protected $menuRepository;

    /**
     * @var int
     */
    protected $menuPerPage = null;

    /**
     * MenuController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param MenuRepository $menuRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        MenuRepository $menuRepository
    ) {
        $this->responseStructured = $responseGeneral;
        $this->menuRepository = $menuRepository;

        $this->menuPerPage = config('view.count.menus');
    }

    /**
     * index menu
     *
     * @authenticated
     *
     * ###Get list menu.
     *
     * @response {
     *  "status": true,
     *  "entity": "list menu"
     * }
     *
     *
     * @return array
     * @throws AuthorizationException
     */
    public function index(): array
    {
        $this->authorize('list', Menu::class);

        $menu = $this->menuRepository->simplePaginate($this->menuPerPage);

        return $this->responseStructured->addEntity($menu)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * Menu save
     *
     * ###Menu save by request.
     *
     * @authenticated
     *
     * @bodyParam title         string  required    The title of the menu(required;filled;max:191). Example: test
     * @bodyParam order         int                 The sort of the menu(numeric). Example: 1
     * @bodyParam parent_id     int                 The sub menu of the menu(numeric). Example: 1
     * @bodyParam uri           string  required    The slug of the menu(required;). Example: user/create
     * @bodyParam icon          string              The icon of the menu(max:50). Example: fa-black-tie
     *
     * @response {
     *  "status": true,
     *  "entity": "new menu",
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
        $this->authorize('create', Menu::class);

        $menuCreate = $this->menuRepository->create($request->all());
        $menu = $this->menuRepository->find($menuCreate->id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($menu)
            ->addMessage(trans('menu.success.created'), 'success')
            ->getResponse();
    }

    /**
     * Menu preview
     *
     * ###Get menu by id.
     *
     *
     * @queryParam id   required    The id of the menu.
     *
     * @response {
     *  "status": true,
     *  "entity": "menu",
     *  "metadata": "get info menu"
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
     */
    public function show(int $id): array
    {
        /** @var Menu $menu */
        $menu = $this->menuRepository->find($id);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured
            ->addEntity($menu)
            ->getResponse();
    }

    /**
     * Menu update
     *
     * ###Menu update by request.
     *
     * @authenticated
     *
     * @bodyParam title         string  required    The title of the menu(required;filled;max:191). Example: test
     * @bodyParam order         int                 The sort of the menu(numeric). Example: 1
     * @bodyParam parent_id     int                 The sub menu of the menu(numeric). Example: 1
     * @bodyParam uri           string  required    The slug of the menu(required;). Example: user/create
     * @bodyParam icon          string              The icon of the menu(max:50). Example: fa-black-tie
     *
     * @response {
     *  "status": true,
     *  "entity": "updated name",
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
     */
    public function update(Request $request, int $id): array
    {
        $this->authorize('update', Menu::class);

        $this->menuRepository->update($request->all(), (int)$id);

        $menu = $this->menuRepository->find($id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($menu)
            ->addMessage(trans('menu.success.updated'), 'success')
            ->getResponse();
    }

    /**
     * Remove menu
     *
     * ###Remove menu
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
        /** @var Menu $menu */
        $menu = $this->menuRepository->find($id);

        $this->authorize('delete', $menu);

        $menu->delete();

        return $this->responseStructured
            ->setStatus(true)
            ->addMessage(trans('menu.success.delete'), 'success')
            ->getResponse();
    }
}
