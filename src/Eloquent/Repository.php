<?php

namespace Deseco\Repositories\Eloquent;

use Deseco\Repositories\Builders\CriteriaNameBuilder;
use Deseco\Repositories\Contracts\RepositoryInterface;
use Deseco\Repositories\Eloquent\Criteria\Criteria;
use Deseco\Repositories\Exceptions\RepositoryException;
use Deseco\Repositories\Exceptions\RepositoryMethodNotExistsException;
use Deseco\Repositories\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;

/**
 * Class Repository
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
    public function save(array $data)
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
    public function fillUpdate(array $data, $id)
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
        $methods = func_get_args();
        $methods = array_depth($methods) >= 2 ? array_shift($methods) : $methods;

        $criteria = (new CriteriaNameBuilder(get_class($this)))->getNamespace();

        foreach ($methods as $key => $value) {
            $args = is_array($value) ? $value : [];
            $method = count($args) ? $key : $value;

            $object = (class_exists($criteria) && method_exists($criteria, $method)) ?
                new $criteria($this->model) : $this;

            if (! $object instanceof Criteria && ! method_exists($object, $method)) {
                throw new RepositoryMethodNotExistsException("Method {$method} does not exists in repositories.");
            }

            count($args) ? call_user_func_array([$object, $method], $args) : $object->{$method}();

            if ($object instanceof Criteria) {
                $this->model =  $object->getModel();
            }
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
