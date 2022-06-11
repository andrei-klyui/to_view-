<?php

namespace App\Presenters;

use App\Transformers\StatisticTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class StatisticPresenter.
 *
 * @package namespace App\Presenters;
 */
class StatisticPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new StatisticTransformer();
    }
}
