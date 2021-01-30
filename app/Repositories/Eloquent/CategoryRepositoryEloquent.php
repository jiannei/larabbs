<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CategoryRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Models\Category;
use App\Repositories\Validators\CategoryValidator;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
