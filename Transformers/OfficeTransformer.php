<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Office;

/**
 * Class OfficeTransformer.
 *
 * @package namespace App\Transformers;
 */
class OfficeTransformer extends TransformerAbstract
{
    /**
     * Transform the Office entity.
     *
     * @param \App\Models\Office $model
     *
     * @return array
     */
    public function transform(Office $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
