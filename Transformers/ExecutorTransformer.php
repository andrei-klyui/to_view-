<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Executor;

/**
 * Class ExecutorTransformer.
 *
 * @package namespace App\Transformers;
 */
class ExecutorTransformer extends TransformerAbstract
{
    /**
     * Transform the Executor entity.
     *
     * @param \App\Models\Executor $model
     *
     * @return array
     */
    public function transform(Executor $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
