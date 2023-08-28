<?php

namespace App;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\String\u;


function search_model($model, $attrs, $query, $relation = null)
{

    $queryBuilder = $model;

    if ((!is_null($attrs))) {
        $queryBuilder->where($attrs[0], 'LIKE', '%' . $query . '%');
        for ($i = 1; $i < count($attrs); $i++) {
            $queryBuilder->orWhere($attrs[$i], 'LIKE', '%' . $query . '%');
        }

    }

    if (!is_null($relation)) {
        $queryBuilder->orwhereHas($relation[0], function ($q) use ($query, $relation) {
            $q->where($relation[1], 'LIKE', '%' . $query . '%');
        })->get();
    }

    return $queryBuilder->get();

}






