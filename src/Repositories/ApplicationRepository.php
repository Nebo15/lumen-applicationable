<?php
namespace Nebo15\LumenApplicationable\Repositories;

use Illuminate\Database\Eloquent\Model;
use Nebo15\LumenApplicationable\Exceptions\RepositoryException;

class ApplicationRepository
{
    protected $modelClassName = 'Nebo15\LumenApplicationable\Models\Application';
    protected $observerClassName = '';

    /** @var Model $model */
    private $model;

    public function __construct()
    {
        if (!class_exists($this->modelClassName)) {
            throw new RepositoryException("Model " . $this->modelClassName . " not found");
        }
        $this->model = new $this->modelClassName;
        if (!($this->model instanceof Model)) {
            throw new RepositoryException(
                "Model $this->modelClassName should be instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $id
     * @return Model
     */
    public function read($id)
    {
        return call_user_func_array([$this->modelClassName, 'find'], [$id]);
    }

    /**
     * @param null $size
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function readList($size = null)
    {
        return call_user_func_array([$this->modelClassName, 'paginate'], [intval($size)]);
    }

    public function createOrUpdate($values, $id = null)
    {
        $model = $id ? $this->read($id) : $this->model->newInstance();
        $model->fill($values)->save();

        return $model;
    }

    public function copy($id)
    {
        $model = $this->read($id);
        $values = $model->getAttributes();
        unset($values[$model->getKeyName()]);

        return $this->createOrUpdate($values);
    }

    public function delete($id)
    {
        return $this->read($id)->delete();
    }
}
