<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface BaseApiControllerInterface
{
    /**
     * @param Request $request
     * @return null|Response
     */
    public function indexAction(Request $request): ?Response;
}