<?php

namespace App\Presenters;

use App\Transformers\ExecutorTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ExecutorPresenter.
 *
 * @package namespace App\Presenters;
 */
class ExecutorPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ExecutorTransformer();
    }
}
