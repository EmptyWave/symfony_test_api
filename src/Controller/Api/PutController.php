<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\{Balance, Transaction, User};
use App\Service\TransactionService;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class PutController extends BaseApiController
{
    /**
     * @Route("/api/", name="api_put", methods={"PUT"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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

        if (empty($transaction)) {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }
        if ($transaction->getStatus() !== Transaction::ERR_BD) {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }

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

        return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
    }
}