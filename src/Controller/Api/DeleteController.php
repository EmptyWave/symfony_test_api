<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Service\Token;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends BaseApiController
{
    /**
     * @Route("/api/delete/", name="api_delete", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $token = $request->query->get('token');

        if (!Token::isValid($token)) {
            return $this->errResponse("Wrong token", Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $idTransaction = $request->query->get('transaction');

        if (empty($idTransaction)) {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }

        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->find($idTransaction);

        if ($transaction) {
            $transaction->setStatus(Transaction::CANCELED);
            $transaction->update();
            return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
        } else {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }
    }
}