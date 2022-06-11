<?php

namespace App\Models\Filters;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Builder;

class IssueFilter extends QueryFilter
{
    /**
     * @param int $id
     * @return Builder
     */
    public function status($id): Builder
    {
        if (is_array($id)) {
            return $this->builder->whereIn('status', $id);
        }

        return $this->builder->where('status', $id);
    }

    /**
     * @return Builder
     */
    public function archiveStatus(): Builder
    {
        return $this->builder->where('status', Issue::STATUS_ARCHIVED);
    }

    /**
     * @param int $id
     * @return Builder
     */
    public function priority($id): Builder
    {
        if (is_array($id)) {
            return $this->builder->whereIn('priority', $id);
        }

        return $this->builder->where('priority', $id);
    }

    /**
     * @param bool|int $id
     * @return Builder
     */
    public function user($id = null): Builder
    {
        $id = $id ? $id : \Auth::id();

        return $this->builder->where('user_id', $id);
    }

    /**
     * @param bool $from
     * @return Builder
     */
    public function from($from = false): Builder
    {
        $builder = $this->builder;

        if ($from = $this->request->get('from')) {
            $builder->whereDate('due_date', '>=', $from);
        }

        return $this->builder;
    }

    /**
     * @param bool $to
     * @return Builder
     */
    public function to($to = false): Builder
    {
        $builder = $this->builder;

        if ($to = $this->request->get('to')) {
            $builder->whereDate('due_date', '<=', $to);
        }

        return $this->builder;
    }

    /**
     * @param bool $title
     * @return Builder
     */
    public function title($title = false): Builder
    {
        if ($title) {
            $this->whereLike('issues.title', $title);
        }

        return $this->builder;
    }

    /**
     * Added sort by asc
     *
     * @param string $sortBy
     * @return Builder
     */
    public function sortByAsc($sortBy = ''): Builder
    {
        return $this->sortBy($sortBy, 'ASC');
    }

    /**
     * Added sort by
     *
     * @param string $sortBy
     * @param string $direction
     * @return Builder
     */
    protected function sortBy($sortBy = '', $direction = 'ASC'): Builder
    {
        switch ($sortBy) {
            case 'priority':
                $this->builder->orderByRaw(
                    "FIELD(`$sortBy`, '" . implode("','", Issue::prioritiesOrigin()) . "') $direction"
                );
                break;

            case 'status':
                $this->builder->orderByRaw(
                    "FIELD(`$sortBy`, '" . implode("','", Issue::statusesOrigin()) . "') $direction"
                );
                break;

            case 'type':
                $this->builder->orderByRaw(
                    "FIELD(`$sortBy`, '" . implode("','", Issue::typesOrigin()) . "') $direction"
                );
                break;

            case 'due_date':
                $this->builder->orderByRaw(
                    "`$sortBy` $direction"
                );
                break;

            default:
                break;
        };

        return $this->builder;
    }

    /**
     * Added sort by desc
     *
     * @param string $sortBy
     * @return Builder
     */
    public function sortByDesc($sortBy = ''): Builder
    {
        return $this->sortBy($sortBy, 'DESC');
    }

    /**
     * @param int $reporter
     * @return Builder
     */
    public function reporter($reporter = null): Builder
    {
        if ($reporter) {
            $this->builder->where('reporter_id', '=', $reporter);
        }

        return $this->builder;
    }

    /**
     * @param int $executor
     * @return Builder
     */
    public function executor($executor = null): Builder
    {
        if ($executor) {
            $this->builder->where('executor_id', '=', $executor);
        }

        return $this->builder;
    }
}
