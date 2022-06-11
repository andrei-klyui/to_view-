<?php

namespace App\Presenters;

use App\Transformers\RoleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class RolesPresenter.
 *
 * @package namespace App\Presenters;
 */
class RolePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new RoleTransformer();
    }
}
