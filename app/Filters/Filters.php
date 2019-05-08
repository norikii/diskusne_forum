<?php
/**
 * Created by PhpStorm.
 * User: norik
 * Date: 13/04/19
 * Time: 13:07
 */

namespace App\Filters;


use Illuminate\Http\Request;

abstract class Filters
{
    /** @var Request  */
    protected $request;

    /** @var  */
    protected $builder;

    /** @var array  */
    protected $filters = [];

    /**
     * ThreadFilters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Select user where user_id equals passed argument
     *
     * @param $builder
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        collect($this->getFilters())
            ->filter(function ($value, $filter) {
                return method_exists($this, $filter);
            })
            ->each(function ($value, $filter) {
                $this->$filter($value);
            });

        return $this->builder;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->request->only($this->filters);
    }
}
