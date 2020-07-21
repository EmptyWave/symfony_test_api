<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class GetController extends BaseApiController
{
    /**
     * @Route("/api/get/", name="api_get", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $idTransaction = $request->query->get('transaction');

        if (empty($idTransaction)) {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }

        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->find($idTransaction);

        if ($transaction) {
            return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
        } else {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }
    }
}