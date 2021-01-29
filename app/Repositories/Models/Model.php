<?php


namespace App\Repositories\Models;

use App\Support\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    use SerializeDate;
}
