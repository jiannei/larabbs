<?php

namespace App\Repositories\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;

class TopicCriteria extends Criteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->request->get('order') === 'recent') {
            $model->orderBy('created_at', 'desc');
        } else {
            $model->orderBy('updated_at', 'desc');
        }

        return $model;
    }
}
