<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MyEntityManager;

class TransactionController extends FOSRestController
{
    /**
     * @Route("/api/", name="api_index", methods={"GET", "HEAD"})
     * @return Response
     */
    public function indexAction()
    {
        return new Response('<html><body>API here!</body></html>');
    }

}