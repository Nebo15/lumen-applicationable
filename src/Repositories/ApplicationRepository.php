<?php
namespace Nebo15\LumenApplicationable\Repositories;

use Nebo15\REST\AbstractRepository;

class ApplicationRepository extends AbstractRepository
{
    protected $modelClassName = 'Nebo15\LumenApplicationable\Models\Application';
    protected $observerClassName = '';
}
