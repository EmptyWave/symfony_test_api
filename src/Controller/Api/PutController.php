<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\{Balance, Transaction, User};
use App\Service\{Token, TransactionService};
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class PutController extends BaseApiController
{
    /**
     * @Route("/api/put/", name="api_put", methods={"PUT"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction(Request $request): Response
    {
        $token = $request->query->get('token');
        if (!Token::isValid($token)) {
            return $this->errResponse(json_encode($token), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        $request->query->remove('token');

        $idTransaction = $request->query->get('transaction');
        if (empty($idTransaction)) {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }
        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->find($idTransaction);

        if ($transaction) {
            if ($transaction->getStatus() == Transaction::ERR_BD) {
                $userFrom = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->find($transaction->getFromId());

                $userTo = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->find($transaction->getFromId());

                $balanceFrom = $this->getDoctrine()
                    ->getRepository(Balance::class)
                    ->find($userFrom->getAccountId());

                $balanceTo = $this->getDoctrine()
                    ->getRepository(Balance::class)
                    ->find($userTo->getAccountId());

                (new TransactionService())->executeTransaction($balanceFrom, $balanceTo, $transaction);
            } else {
                return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
            }
        } else {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }


        return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
    }
}