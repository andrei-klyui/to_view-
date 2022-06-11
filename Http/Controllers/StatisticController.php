<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseGeneral;
use App\Repositories\StatisticRepository;

/**
 * @group Statistic
 *
 * ###APIs for managing statistic request
 *
 *
 * Class StatisticController
 * @package App\Http\Controllers
 */
class StatisticController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var StatisticRepository
     */
    protected $statisticRepository;

    /**
     * StatisticController constructor.
     * @param ResponseGeneral $responseGeneral
     * @param StatisticRepository $statisticRepository
     */
    public function __construct(
        ResponseGeneral $responseGeneral,
        StatisticRepository $statisticRepository
    ) {
        $this->responseStructured = $responseGeneral;
        $this->statisticRepository = $statisticRepository;
    }

    /**
     * Statistic Issues by Priorities
     *
     * ###Get count issues by priorities.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "metadata": "statisticByStatuses[]"
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
    public function issuesByPriorities()
    {
        try {
            $statisticList = $this->statisticRepository->getCountIssuesByPriorities(\Auth::id());
            $statistic = $this->reformatResponseFromDB($statisticList);

            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {

            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }

        $this->responseStructured->addMetadata($statistic, 'statisticByPriorities');

        return $this->responseStructured->getResponse();
    }

    /**
     * Statistic Issues by Statuses
     *
     * ###Get count issues by statuses.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "metadata": "statisticByStatuses[]"
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
    public function issuesByStatuses()
    {
        try {
            $statisticList = $this->statisticRepository->getCountIssuesByStatuses(\Auth::id());
            $statistic = $this->reformatResponseFromDB($statisticList);
            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }
        $this->responseStructured->addMetadata($statistic, 'statisticByStatuses');

        return $this->responseStructured->getResponse();
    }

    /**
     * Statistic Issues by Categories
     *
     * ###Get count issues by categories.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "metadata": "statisticByCategories[]"
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
    public function issuesByCategories()
    {
        try {
            $statisticList = $this->statisticRepository->getCountIssuesByCategories(\Auth::id());
            $statistic = $this->reformatResponseFromDB($statisticList);
            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }
        $this->responseStructured->addMetadata($statistic, 'statisticByCategories');

        return $this->responseStructured->getResponse();
    }

    /**
     * Reformat response from DB to collection
     *
     * @param $listItems
     * @return \Illuminate\Support\Collection
     */
    private function reformatResponseFromDB($listItems)
    {
        $result = collect();
        foreach ($listItems as $item) {
            $attributes = $item->attributesToArray();
            $result->put($attributes['item_statistic'], $attributes['count']);
        }

        return $result;
    }
}
