<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\LinkRepository;
use App\Repositories\Models\Link;
use App\Repositories\Validators\LinkValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class LinkRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class LinkRepositoryEloquent extends BaseRepository implements LinkRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Link::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
