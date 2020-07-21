<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\{Response, Request};
use FOS\RestBundle\Controller\FOSRestController;

abstract class BaseApiController extends FOSRestController implements BaseApiControllerInterface
{
    /**
     * @param Request $request
     * @return null|Response
     */
    abstract public function indexAction(Request $request): ?Response;

    /**
     * @param $content
     * @param $code
     * @param string $contentType
     * @return Response
     */
    protected function okResponse($content, $code, $contentType = 'application/json'): Response
    {
        $response = new Response();
        $response->setContent(json_encode($content));
        $response->setStatusCode($code);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    /**
     * @param $content
     * @param $code
     * @return Response
     */
    protected function errResponse($content, $code): Response
    {
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode($code);
        return $response;
    }
}