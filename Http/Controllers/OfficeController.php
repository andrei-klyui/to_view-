<?php

namespace App\Http\Controllers;

use App\Models\Filters\FilterTrait;
use App\Repositories\OfficeRepository;
use App\Http\Responses\ResponseGeneral;
use App\Events\OfficeRead;
use App\Models\Office;
use App\Models\Filters\OfficeFilter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="ToDo API", version="0.1")
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="Authorization",
 *      type="http",
 *      scheme="Bearer",
 *      bearerFormat="JWT",
 * ),
 */

class OfficeController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var officeRepository
     */
    protected $officeRepository;

    /**
     * @var int
     */
    protected $officesPerPage = null;

    /**
     * OfficeController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param OfficeRepository $officeRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        OfficeRepository $officeRepository
    )
    {
        $this->responseStructured = $responseGeneral;
        $this->officeRepository = $officeRepository;

        $this->officesPerPage = config('view.count.offices');
    }

    /**
     * @OA\Get(
     *     path="/api/office",
     *     tags={"Office"},
     *     security={{"bearerAuth":{}}},
     *     summary="Offices",
     *     description="Offices",
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                       description=""
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entity",
     *                          type="object",
     *                           @OA\Property(
     *                              property="current_page",
     *                              type="integer",
     *                              description=""
     *                          ),
     *                          @OA\Property(
     *                              property="data",
     *                              type="array",
     *                              description="",
     *                              @OA\Items(
     *                                  type="object",
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string"
     *                                  ),
     *                              )
     *
     *                          ),
     *                          @OA\Property(
     *                              property="first_page_url",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="from",
     *                              type="integer"
     *                          ),
     *                          @OA\Property(
     *                              property="last_page",
     *                              type="integer"
     *                          ),
     *                          @OA\Property(
     *                              property="last_page_url",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="next_page_url",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="path",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="per_page",
     *                              type="integer"
     *                          ),
     *                          @OA\Property(
     *                              property="prev_page_url",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="to",
     *                              type="integer"
     *                          ),
     *                          @OA\Property(
     *                              property="total",
     *                              type="integer"
     *                          ),
     *
     *                      )
     *                  ),
     *             ),
     *             example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "current_page": 1,
     *                          "data": {
     *                              {
     *                                  "id": 5,
     *                                  "name": "Test",
     *                                  "created_at": "2021-08-09 19:45:04",
     *                                  "updated_at": "2021-08-09 19:45:04"
     *                              }
     *                          },
     *                          "first_page_url": "http://your_domain/api/office?page=1",
     *                          "from": 1,
     *                          "last_page": 1,
     *                          "last_page_url": "http://your_domain/api/office?page=1",
     *                          "next_page_url": null,
     *                          "path": "http://your_domain/api/office",
     *                          "per_page": 10,
     *                          "prev_page_url": null,
     *                          "to": 1,
     *                          "total": 1
     *                      }
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function index(OfficeFilter $filters): array
    {
        $this->authorize('list', Office::class);

        $offices = $this->officeRepository->getList($filters);

        return $this->responseStructured
            ->addEntity($offices)
            ->setStatus(true)
            ->getResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/office/{id}",
     *     tags={"Office"},
     *     summary="Get office by ID",
     *     description="Get office by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *        name="id", in="path",required=true, @OA\Schema(type="integer"), description="office id",
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entity",
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                          ),
     *                      )
     *                  ),
     *             ),
     *             example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "id": 5,
     *                          "name": "Test",
     *                          "created_at": "2021-08-09 19:45:04",
     *                          "updated_at": "2021-08-09 19:45:04"
     *                      }
     *                  },
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function show(int $id): array
    {
        $office = $this->officeRepository->find($id);

        $this->responseStructured->addEntity($office);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Data for create new office
     *
     * ###Get data for view form to create new office.
     *
     * @throws AuthorizationException
     */
    public function create(): array
    {
        $this->authorize('create', Office::class);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * @OA\Post(
     *     path="/api/office/save",
     *     tags={"Office"},
     *     security={{"bearerAuth":{}}},
     *     summary="Save office",
     *     description="Office save by request",
     *     @OA\Parameter(
     *        name="name", in="query", required=true, @OA\Schema(type="string"), description="New office name",
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entity",
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                          ),
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="object",
     *                      @OA\Property(
     *                          property="success",
     *                          type="string",
     *                      ),
     *                  )
     *             ),
     *             example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "id": 5,
     *                          "name": "Test",
     *                          "created_at": "2021-08-09 19:45:04",
     *                          "updated_at": "2021-08-09 19:45:04"
     *                      }
     *                  },
     *                  "message": {
     *                      "success": "Office successfully saved"
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function store(Request $request): array
    {
        $this->authorize('create', Office::class);

        $officeCreate = $this->officeRepository->create($request->all());
        $office = $this->officeRepository->find($officeCreate->id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($office)
            ->addMessage(trans('office.success.created'), 'success')
            ->getResponse();
    }

    /**
     * Edit office
     *
     * ###Get data for edit office.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "office data",
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
     */
    public function edit(int $id): array
    {
        $office = $this->officeRepository->getById($id);

        $this->authorize('update', $office);

        $this->responseStructured->addEntity($office);
        $this->responseStructured->setStatus(true);

        event(new OfficeRead($office->id));

        return $this->responseStructured->getResponse();
    }

    /**
     * @OA\Put(
     *     path="/api/office/update/{id}",
     *     tags={"Office"},
     *     security={{"bearerAuth":{}}},
     *     summary="Update office",
     *     description="Update office",
     *     @OA\Parameter(
     *        name="id", in="path",required=true, @OA\Schema(type="integer"), description="office id",
     *     ),
     *     @OA\Parameter(
     *        name="name", in="query", required=true, @OA\Schema(type="string"), description="office name",
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entity",
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                          ),
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="object",
     *                      @OA\Property(
     *                          property="success",
     *                          type="string",
     *                      ),
     *                  )
     *             ),
     *             example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "id": 5,
     *                          "name": "Test",
     *                          "created_at": "2021-08-09 19:45:04",
     *                          "updated_at": "2021-08-09 19:45:04"
     *                      }
     *                  },
     *                  "message": {
     *                      "success": "Office successfully updated"
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function update(Request $request, int $id): array
    {
        $this->authorize('update', Office::class);

        $this->officeRepository->update($request->all(), (int)$id);
        $office = $this->officeRepository->find($id);

        return $this->responseStructured
            ->setStatus(true)
            ->addEntity($office)
            ->addMessage(trans('office.success.updated'), 'success')
            ->getResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/office/{id}/floors",
     *     tags={"Office"},
     *     summary="Office floors with rooms",
     *     description="Get office floors.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *        name="id", in="path",required=true, @OA\Schema(type="integer"), description="office id",
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entity",
     *                          type="object",
     *                          @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="floors",
     *                              type="array",
     *                              @OA\Items()
     *                          ),
     *                      )
     *                  ),
     *             ),
     *             example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "id": 5,
     *                          "name": "Test",
     *                          "created_at": "2021-08-09 19:45:04",
     *                          "updated_at": "2021-08-09 19:45:04",
     *                          "floors": {}
     *                      }
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function floors(int $id): array
    {
        $office = $this->officeRepository
            ->with('floors.rooms')
            ->find($id);

        $this->responseStructured->setStatus(true);

        return $this->responseStructured
            ->addEntity($office)
            ->getResponse();
    }

    /**
     * @OA\Delete(
     *     path="/api/office/delete/{id}",
     *     tags={"Office"},
     *     summary="Delete office",
     *     description="Delete office",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *        name="id", in="path",required=true, @OA\Schema(type="integer"), description="office id",
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="object",
     *                      @OA\Property(
     *                          property="success",
     *                          type="string",
     *                      ),
     *                  )
     *             ),
     *             example={
     *                 "status": true,
     *                  "message": {
     *                      "success": "Office deleted successfully"
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function destroy(int $id): array
    {
        /** @var Office $office */
        $office = $this->officeRepository->find($id);

        $this->authorize('delete', $office);

        $office->delete();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('office.success.delete'), 'success');

        return $this->responseStructured->getResponse();
    }
}
