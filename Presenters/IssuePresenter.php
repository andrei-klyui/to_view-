<?php

namespace App\Presenters;

use App\Transformers\IssueTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class IssuePresenter.
 *
 * @package namespace App\Presenters;
 */
class IssuePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new IssueTransformer();
    }
}
