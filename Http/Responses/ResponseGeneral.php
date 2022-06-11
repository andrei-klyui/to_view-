<?php

namespace App\Http\Responses;

/**
 * Class for creating structure response.
 * Structure of response is following:
 *
 * +- entity    - []
 * + status     - true | false
 * +- message   - []
 * +- metadata  - []
 *
 */

/**
 * Class ResponseGeneral
 * @package App\Http\Responses
 */
class ResponseGeneral extends ResponseStructuredGeneral
{
    /**
     * Lists params for structure response.
     */
    private const PARAM_ENTITY      = 'entity';
    private const PARAM_STATUS      = 'status';
    private const PARAM_MESSAGE     = 'message';
    private const PARAM_METADATA    = 'metadata';

    /**
     * ResponseGeneral constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    /**
     * Initialize collection
     */
    protected function init()
    {
        $this->add(ResponseGeneral::PARAM_STATUS, false);
    }

    /**
     * Clean general collection
     */
    public function clean()
    {
        parent::clean();

        $this->init();
    }

    /**
     * @param $entity
     * @param null $title
     * @return $this
     */
    public function addEntity($entity, $title = null)
    {
        $this->addMetadata($entity, ResponseGeneral::PARAM_ENTITY);

        return $this;
    }

    /**
     * @param $message
     * @param null $title
     * @return $this
     */
    public function addMessage($message, $title = null)
    {
        $this->push(ResponseGeneral::PARAM_MESSAGE, $title, $message);

        return $this;
    }

    /**
     * @param $metadata
     * @param null $title
     * @return $this
     */
    public function addMetadata($metadata, $title = null)
    {
        $this->push(ResponseGeneral::PARAM_METADATA, $title, $metadata);

        return $this;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->add(ResponseGeneral::PARAM_STATUS, $status);

        return $this;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->get(ResponseGeneral::PARAM_STATUS, false);
    }
}
