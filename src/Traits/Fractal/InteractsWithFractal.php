<?php

namespace Deseco\Repositories\Traits\Fractal;

use Illuminate\Http\Request;

trait InteractsWithFractal
{
    abstract function map();

    public function transform($filters)
    {
        $data = [];

        $map = $this->map();

        foreach($filters as $name => $filter) {
            $mapItem = $map[$name];

            foreach ($filter as $key => $value) {
                $data[$mapItem[$key]] = $value;
            }
        }

        return $data;
    }

    public function filters()
    {
        if($this->request instanceof Request) {
            return parent::filters();
        }

        return $this->transform($this->request);
    }
}