<?php
 namespace Nebo15\LumenApplicationable\Controllers;

use Illuminate\Http\Request;
use Nebo15\LumenApplicationable\Exceptions\ControllerException;
use Illuminate\Contracts\Validation\ValidationException;
use Nebo15\LumenApplicationable\Repositories\ApplicationRepository;
use Nebo15\LumenApplicationable\Response;

class Controller extends \Laravel\Lumen\Routing\Controller
{

    private $repository;

    protected $request;

    protected $response;

    protected $repositoryClassName;

    protected $validationRules = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationException($validator);
    }

    protected function getRepository()
    {
        if (!$this->repository) {
            if (!$this->repositoryClassName) {
                throw new ControllerException("You should set \$repositoryClassName");
            }
            $this->repository = new $this->repositoryClassName;
            if (!($this->repository instanceof ApplicationRepository)) {
                throw new ControllerException("Repository $this->repositoryClassName should be instance of Nebo15\\LumenApplicationable\\Repositories\\ApplicationRepository");
            }
        }

        return $this->repository;
    }

    public function create()
    {
        $this->validateRoute();

        return $this->response->json(
            $this->getRepository()->createOrUpdate($this->request->all())->toArray(),
            Response::HTTP_CREATED
        );
    }

    public function copy($id)
    {
        return $this->response->json(
            $this->getRepository()->copy($id)->toArray()
        );
    }

    public function read($id)
    {
        return $this->response->json($this->getRepository()->read($id)->toArray());
    }

    public function update($id)
    {
        $this->validateRoute();

        return $this->response->json(
            $this->getRepository()->createOrUpdate($this->request->request->all(), $id)->toArray()
        );
    }

    public function delete($id)
    {
        return $this->response->json(
            $this->getRepository()->delete($id)
        );
    }

    public function validateRoute()
    {
        $action = debug_backtrace()[1]['function'];
        if (isset($this->validationRules[$action])) {
            $this->validate($this->request, $this->validationRules[$action]);
        }
    }
}
