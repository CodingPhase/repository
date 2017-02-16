<?php

namespace Deseco\Repositories\Eloquent;

use Deseco\Repositories\Contracts\RepositoryInterface;
use Deseco\Repositories\Exceptions\RepositoryException;
use Deseco\Repositories\Exceptions\RepositoryMethodNotExistsException;
use Deseco\Repositories\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;

/**
 * Class Repository
 *
 * @package Deseco\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $newModel;


    /**
     * Repository constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return mixed
     */
    abstract public function model();

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        return $this->model->first($columns);
    }

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 25, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function saveModel(array $data)
    {
        foreach ($data as $key => $value) {
            $this->model->$key = $value;
        }

        return $this->model->save();
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     *
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param array $data
     * @param $id
     *
     * @return bool
     */
    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function byId($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function by($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function allBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param $where
     * @param array $columns
     * @param bool $or
     *
     * @return mixed
     */
    public function byWhere($where, $columns = ['*'], $or = false)
    {
        $method = $or ? 'orWhere' : 'where';

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $this->model = $this->model->{$method}($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $this->model = $this->model->{$method}($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $this->model = $this->model->{$method}($field, '=', $search);
                }
            } else {
                $this->model = $this->model->{$method}($field, '=', $value);
            }
        }

        return $this->model->get($columns);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    /**
     * @param $eloquentModel
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Deseco\Repositories\Exceptions\RepositoryException
     */
    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);

        if (!$this->newModel instanceof Model) {
            throw new RepositoryException(
                "Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return $this->model = $this->newModel;
    }

    /**
     * @return $this
     * @throws \Deseco\Repositories\Exceptions\RepositoryMethodNotExistsException
     */
    public function matching()
    {
        $methods = array_flatten(func_get_args());

        foreach ($methods as $method) {
            if (! method_exists($this, $method)) {
                throw new RepositoryMethodNotExistsException(
                    "Class " . get_class($this) . " don't contain method {$method}."
                );
            }

            $this->{$method}();
        }

        return $this;
    }

    /**
     * @param \Deseco\Repositories\Filters\QueryFilters $filters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(QueryFilters $filters)
    {
        return $filters->apply($this->model->query());
    }
}
