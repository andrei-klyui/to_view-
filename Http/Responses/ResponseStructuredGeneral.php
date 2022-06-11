<?php

namespace App\Http\Responses;

use Illuminate\Support\Collection;

/**
 * Class ResponseStructuredGeneral
 * @package App\Http\Responses
 */
class ResponseStructuredGeneral
{
    /**
     * @var Collection
     */
    private $collectionResponse = null;

    /**
     * ResponseStructuredGeneral constructor.
     */
    public function __construct()
    {
        if ($this->collectionResponse == null) {
            $this->clean();
        }
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->collectionResponse->all();
    }

    /**
     * @return string
     */
    public function buildResponseToJson()
    {
        return $this->collectionResponse->toJson();
    }

    /**
     * @param $key
     * @param $value
     * @return Collection
     */
    protected function add($key, $value)
    {
        return $this->collectionResponse->put($key, $value);
    }

    /**
     * Clean general collection
     */
    public function clean()
    {
        $this->collectionResponse = collect();
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function get($key, $value)
    {
        $result = $this->collectionResponse->get($key, $value);
        if ($result instanceof Collection) {
            return $result->take($result->count());
        }

        return $result;
    }

    /**
     * @param $keyInCollection
     * @param $key
     * @param $value
     * @return Collection
     */
    protected function push($keyInCollection, $key, $value)
    {

        if (!$this->collectionResponse->has($keyInCollection) && $key === null) {
            return $this->add($keyInCollection, $value);
        }

        $this->addCollection($keyInCollection);

        return $this->addItemToCollection($keyInCollection, $key, $value);
    }

    /**
     * @param $keyInCollection
     * @return Collection
     */
    private function addCollection($keyInCollection)
    {
        if (!$this->collectionResponse->has($keyInCollection)) {
            return $this->add($keyInCollection, collect());
        }

        $item = $this->collectionResponse->get($keyInCollection);

        if (!($item instanceof Collection)) {
            $this->add($keyInCollection, collect());

            return $this->addItemToCollection($keyInCollection, null, $item);
        }

        return $item;
    }

    /**
     * @param $keyInCollection
     * @param $key
     * @param $value
     * @return Collection
     */
    private function addItemToCollection($keyInCollection, $key, $value)
    {
        /**
         * @var Collection $collection
         */

        $collection = $this->collectionResponse->get($keyInCollection);

        return $collection->put($key, $value);
    }
}
