<?php
namespace Nebo15\LumenApplicationable\Controllers;

use Nebo15\LumenApplicationable\ApplicationableHelper;
use Nebo15\LumenApplicationable\Contracts\ApplicationableUser as ApplicationableUserContract;
use Nebo15\LumenApplicationable\Exceptions\AccessDeniedException;
use Nebo15\LumenApplicationable\Exceptions\AclRequiredException;
use Nebo15\LumenApplicationable\Exceptions\TryingToAddDuplicateUserException;
use Nebo15\LumenApplicationable\Exceptions\UserException;
use Nebo15\LumenApplicationable\Models\Application;
use Nebo15\LumenApplicationable\Response;

class ApplicationController extends Controller
{
    protected $repositoryClassName = 'Nebo15\LumenApplicationable\Repositories\ApplicationRepository';

    protected $validationRules = [
        'create' => [
            'title' => 'required|string',
            'description' => 'string',
            'settings' => 'array',
        ],
        'updateApplication' => [
            'title' => 'sometimes|required|string',
            'description' => 'string',
        ],
        'updateUser' => [
            'user_id' => 'required',
            'role' => 'sometimes|required|string|not_in:admin',
            'scope' => 'sometimes|required|array',
        ],
        'addUserToProject' => [
            'user_id' => 'required',
            'role' => 'required|string|not_in:admin',
            'scope' => 'required|array',
        ],
        'deleteUser' => [
            'user_id' => 'required|string',
        ],
        'createConsumer' => [
            'description' => 'string',
            'scope' => 'required|array',
        ],
        'updateConsumer' => [
            'description' => 'string',
            'scope' => 'required|array',
        ],
        'deleteConsumer' => [
            'client_id' => 'required|string',
        ],
    ];

    /**
     * Create Project
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Nebo15\LumenApplicationable\Exceptions\ControllerException
     * @throws \Nebo15\LumenApplicationable\Exceptions\UserException
     */
    public function create()
    {
        $this->validateRoute();
        $user = $this->request->user();

        if (!$user instanceof ApplicationableUserContract) {
            throw new UserException("Model " . get_class($this->request->user()) . " should be implement ApplicationableUserContract");
        }

        $application = $this->getRepository()->createOrUpdate($this->request->all());
        $application->setUser(
            [
                'user_id' => $user->getId(),
                'role' => 'admin',
                'scope' => config('applicationable.scopes.users'),
            ]
        )->save();

        return $this->response->json(
            $application->toArray(),
            Response::HTTP_CREATED
        );
    }

    public function updateApplication()
    {
        $this->validateRoute();
        $user = $this->request->user();

        if (!$user instanceof ApplicationableUserContract) {
            throw new UserException("Model " . get_class($this->request->user()) . " should be implement ApplicationableUserContract");
        }
        $application = $this->getRepository()->createOrUpdate($this->request->all(),
            ApplicationableHelper::getApplicationId());

        return $this->response->json(
            $application->toArray(),
            Response::HTTP_OK
        );
    }

    public function getCurrentUser()
    {
        return $this->response->json($this->request->user()->getApplicationUser(), Response::HTTP_OK);
    }

    /**
     * @param \Nebo15\LumenApplicationable\Models\Application $application
     * @return mixed
     * @throws \Nebo15\LumenApplicationable\Exceptions\TryingToAddDuplicateUserException
     * @throws \Nebo15\LumenApplicationable\Exceptions\AclRequiredException
     */
    public function addUserToProject(Application $application)
    {
        $current_user = $this->request->user()->getApplicationUser();
        if (!$current_user) {
            throw new AclRequiredException('ACL required for this route');
        }
        $this->validationRules['addUserToProject']['scope'] = 'required|array|in:' . join(',', $current_user->scope);
        $this->validateRoute();

        if (!$application->getUser($this->request->get('user_id'))) {
            $application->setUser($this->request->all())->save();
        } else {
            throw new TryingToAddDuplicateUserException('duplicate user');
        }

        return $this->response->json($application->toArray(), Response::HTTP_CREATED);
    }

    public function setProjectAdmin(Application $application)
    {
        $current_user = $this->request->user()->getApplicationUser();
        if (!$current_user->isAdmin()) {
            throw new AccessDeniedException(json_encode([
                'message' => 'Trying set admin',
                'scopes' => ['is_admin'],
            ]));
        }
        $user = $application->getUser($this->request->get('user_id'))->fill([
            'role' => 'admin',
            'scope' => $current_user->scope,
        ]);
        $user_data = $user->toArray();
        $application->deleteUser($this->request->get('user_id'))->save();
        $application->setUser($user_data)->save();

        if (!$user) {
            throw new \HttpException('not_found', 404);
        }

        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }

    public function updateUser(Application $application)
    {
        $current_user = $this->request->user()->getApplicationUser();

        /**
         * Temporary feature, user can't update himself
         */
        if ($current_user->id == $this->request->get('user_id')) {
            return $this->response->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$current_user) {
            throw new AclRequiredException('ACL required for this route');
        }
        $this->validationRules['updateUser']['scope'] = 'required|array|in:' . join(',', $current_user->scope);
        $this->validateRoute();
        $user = $application->getUser($this->request->get('user_id'))->fill($this->request->request->all());
        $user_data = $user->toArray();
        $application->deleteUser($this->request->get('user_id'))->save();
        $application->setUser($user_data)->save();

        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }

    /**
     * @param \Nebo15\LumenApplicationable\Models\Application $application
     * @return mixed
     * @throws \Nebo15\LumenApplicationable\Exceptions\AccessDeniedException
     */
    public function deleteUser(Application $application)
    {
        $this->validationRules['deleteUser']['user_id'] = 'required|string|not_in:' . $this->request->user()->getId();
        $this->validateRoute();

        if ($application->getUser($this->request->get('user_id'))->isAdmin() && !$this->request->user()->getApplicationUser()->isAdmin()) {
            throw new AccessDeniedException(json_encode([
                'message' => 'Trying to delete admin',
                'scopes' => ['is_admin'],
            ]));
        }

        $application->deleteUser($this->request->get('user_id'))->save();

        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }

    public function getConsumers(Application $application)
    {
        return $this->response->json($application->consumers()->toArray(), Response::HTTP_OK);
    }

    public function createConsumer(Application $application)
    {
        /** @var Application $application */

        $current_user = $this->request->user()->getApplicationUser();
        $this->validationRules['createConsumer']['scope'] = 'required|array|in:' . join(',', $current_user->scope);
        $this->validateRoute();

        $this->validate(
            $this->request,
            ['scope' => 'required|array|in:' . join(',', config('applicationable.scopes.consumers'))]
        );

        $application->setConsumer([
            'client_id' => $this->generateToken(),
            'client_secret' => $this->generateToken(),
            'description' => $this->request->get('description', ''),
            'scope' => $this->request->get('scope'),
        ])->save();

        return $this->response->json($application->toArray(), Response::HTTP_CREATED);
    }

    public function updateConsumer(Application $application)
    {
        $current_user = $this->request->user()->getApplicationUser();
        $this->validationRules['onsumer']['scope'] = 'required|array|in:' . join(',', $current_user->scope);
        $this->validateRoute();

        $this->validate(
            $this->request,
            ['scope' => 'required|array|in:' . join(',', config('applicationable.scopes.consumers'))]
        );
        $consumer = $application->getConsumer($this->request->get('client_id'))->fill($this->request->request->all());
        $consumer_data = $consumer->toArray();
        $application->deleteConsumer($this->request->get('client_id'))->save();
        $application->setConsumer($consumer_data)->save();

        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }

    public function deleteConsumer(Application $application)
    {
        $this->validateRoute();
        $application->deleteConsumer($this->request->get('client_id'))->save();

        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }

    public function projectsList()
    {
        return $this->response->json(
            Application::where(
                [
                    'users' =>
                        [
                            '$elemMatch' =>
                                [
                                    'user_id' => $this->request->user()->getId(),
                                    'scope' => 'read',
                                ],
                        ],
                ]
            )->get()->toArray()
        );
    }

    /**
     * @param \Nebo15\LumenApplicationable\Models\Application $application
     * @return mixed
     * Get current project
     */
    public function index(Application $application)
    {
        return $this->response->json($application->toArray(), Response::HTTP_OK);
    }


    private function generateToken()
    {
        if (function_exists('mcrypt_create_iv')) {
            $randomData = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        // Last resort which you probably should just get rid of:
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);

        return substr(hash('sha512', $randomData), 0, 40);
    }
}
