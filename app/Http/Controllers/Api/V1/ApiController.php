<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
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
        try{
            // Defined in AppServiceProvider because of the Folder Structure.
            Gate::authorize($ability, [$targetModel, $this->policyClass]);
            return true;
        } catch (AuthorizationException $e) {
            return false;
        }
    }
}
