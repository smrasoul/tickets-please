<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{

    protected $policyClass;

    use ApiResponse;
    public function include (string $relationship): bool
    {
        $param = request()->get('include');

        if(!isset($param)) {
            return false;
        }

        $includedValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includedValues);
    }


    public function isAble($ability, $targetModel)
    {
        // Defined in AppServiceProvider because of the Folder Structure.
        return Gate::authorize($ability, [$targetModel, $this->policyClass]);
    }
}
