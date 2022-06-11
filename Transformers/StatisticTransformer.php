<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Statistic;

/**
 * Class StatisticTransformer.
 *
 * @package namespace App\Transformers;
 */
class StatisticTransformer extends TransformerAbstract
{
    /**
     * Transform the Statistic entity.
     *
     * @param \App\Models\Statistic $model
     *
     * @return array
     */
    public function transform(Statistic $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
