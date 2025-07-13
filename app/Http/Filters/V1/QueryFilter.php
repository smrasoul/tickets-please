<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $request;
    protected $sortables = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if(method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    // to differentiate between includes and filters
    protected function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if(method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $this->builder;
    }

    protected function sort($value)
    {
        $sortAttributes = explode(',', $value);

        foreach ($sortAttributes as $sortAttribute) {
            $direction = 'asc';

            if(strpos($sortAttribute, '-') === 0) {
                $direction = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }

            if (!in_array($sortAttribute, $this->sortables)
                &&
                !array_key_exists($sortAttribute, $this->sortables)
               ){
                continue;
            }

            $columnName = $this->sortables[$sortAttribute] ?? null;

            if ($columnName === null) {
                $columnName = $sortAttribute;
            }

            $this->builder->orderBy($columnName, $direction);
        }
    }
}
