<?php
namespace Nebo15\LumenApplicationable\Controllers;

use Nebo15\LumenApplicationable\Contracts\ApplicationableUserContract;
use Nebo15\LumenApplicationable\Exceptions\UserException;
use Nebo15\LumenApplicationable\Models\Application;
use Nebo15\REST\AbstractController;
use Nebo15\REST\Response;

class ApplicationController extends AbstractController
{
    protected $repositoryClassName = 'Nebo15\LumenApplicationable\Repositories\ApplicationRepository';

    protected $validationRules = [
        'create' => [
            'title' => 'required|string',
            'description' => 'string',
        ],
        'update' => [],
        'consumer' => [
            'description' => 'string',
        ],
    ];

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
                'scope' => config('applicationable.scopes'),
            ]
        )->save();

        return $this->response->json(
            $application->toArray(),
            Response::HTTP_CREATED
        );
    }

    public function consumer()
    {
        /** @var Application $application */
        $application = app()->offsetGet('applicationable.application');
        $application->setConsumer([
            'client_id' => $this->generateToken(),
            'client_secret' => $this->generateToken(),
            'description' => $this->request->get('description', ''),
        ])->save();

        return $this->response->json($application->toArray(), Response::HTTP_CREATED);
    }

    public function index()
    {
        $application = app()->offsetGet('applicationable.application');
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
