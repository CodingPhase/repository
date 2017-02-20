<?php

namespace Deseco\Repositories\Contracts;

/**
 * Interface RepositoryInterface
 * @package Deseco\Repositories\Contracts
 */
interface RepositoryInterface
{

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*']);

    /**
     * @param $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data);

    /**
     * @param array $data
     * @param $id
     *
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);

    /**
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function byId($id, $columns = ['*']);

    /**
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function by($field, $value, $columns = ['*']);

    /**
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function allBy($field, $value, $columns = ['*']);

    /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function byWhere($where, $columns = ['*']);

    /**
     * @return mixed
     */
    public function matching();

    /**
     * @param $callable
     *
     * @return mixed
     */
    public function match($callable);
}
