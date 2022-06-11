<?php

namespace App\Presenters;

use App\Transformers\OfficeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class OfficePresenter.
 *
 * @package namespace App\Presenters;
 */
class OfficePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new OfficeTransformer();
    }
}
