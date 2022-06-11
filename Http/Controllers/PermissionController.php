<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var int
     */
    protected $permissionsPerPage = null;

    /**
     * RoleController constructor.
     * @param ResponseGeneral $responseStructured
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(
        ResponseGeneral $responseStructured,
        PermissionRepository $permissionRepository
    ) {
        $this->responseStructured = $responseStructured;
        $this->permissionRepository = $permissionRepository;

        $this->permissionsPerPage = config('view.count.permissions');
    }

    /**
     *  List permissions
     *
     * ###Get list permissions. With pagination.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "list permissions"
     * }
     *
     * @return array
     */
    public function index()
    {
        $roles = $this->permissionRepository->simplePaginate($this->permissionsPerPage);
        return $this->responseStructured
            ->addEntity($roles)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
