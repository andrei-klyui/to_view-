<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Models\Filters\RoleFilter;
use App\Models\Role;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Validators\PermissionValidator;
use App\Validators\RoleValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RoleValidator
     */
    protected $roleValidator;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var PermissionValidator
     */
    protected $permissionValidator;

    /**
     * @var int
     */
    protected $rolesPerPage = null;

    /**
     * RoleController constructor.
     * @param ResponseGeneral $responseStructured
     * @param RoleValidator $roleValidator
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param PermissionValidator $permissionValidator
     */
    public function __construct(
        ResponseGeneral $responseStructured,
        RoleValidator $roleValidator,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository,
        PermissionValidator $permissionValidator
    ) {
        $this->responseStructured = $responseStructured;
        $this->roleValidator = $roleValidator;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->permissionValidator = $permissionValidator;

        $this->rolesPerPage = config('view.count.roles');
    }

    /**
     *  Get a list of roles per page
     * @return array
     */
    public function index(RoleFilter $filters): array
    {
        $roles = $this->roleRepository->getList($filters);
        $this->responseStructured->addEntity($roles);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Create role
     *
     * ###Get data for create role.
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
        $this->authorize('create', Role::class);

        $permission = $this->permissionRepository->simplePaginate();

        return $this->responseStructured
            ->addMetadata($permission, 'permission')
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthorizationException
     */
    public function store(Request $request): array
    {
        $this->authorize('create', Role::class);

        /** @var Role $roleNew */
        $roleNew = $this->roleRepository->create($request->all());
        $roleNew->givePermissionsTo($request->get('permissions'));

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addEntity($roleNew);
        $this->responseStructured->addMessage(trans('role.success.created'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Edit role
     *
     * ###Get data for edit role.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "role edit",
     *  "metadata": [
     *      "permission",
     *  ]
     * }
     *
     * @return array
     * @throws AuthorizationException
     */
    public function edit(int $id): array
    {
        $this->authorize('update', Role::class);

        $permission = $this->permissionRepository->simplePaginate();
        $role = $this->roleRepository->getByIdWithRelations($id);

        $this->responseStructured->addMetadata($permission, 'permission');
        $this->responseStructured->addEntity($role);
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @throws AuthorizationException
     */
    public function update(Request $request, $id): array
    {
        $role = $this->roleRepository->getRoleById($id);

        $this->authorize('update', $role);

        /** @var Role $roleUpdated */
        $roleUpdated = $this->roleRepository->updateById($request->all(), $id);
        $roleUpdated->syncPermissions($request->get('permissions'));

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addEntity($role);
        $this->responseStructured->addMessage(trans('role.success.updated'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * @param $id
     * @return array
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function destroy($id): array
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        $this->authorize('delete', $role);

        $role->delete();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('role.success.delete'), 'success');

        return $this->responseStructured->getResponse();
    }
}
