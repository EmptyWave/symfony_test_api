<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\{Balance, Transaction, User};
use App\Service\TransactionService;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends BaseApiController
{
    /**
     * @Route("/api/", name="api_post", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @var Balance $balanceTo
     * @var Balance $balanceFrom
     */
    public function indexAction(Request $request): Response
    {
        $data = $request->query->all();

        foreach ($data as $prop => $value) {
            if (empty($value) || !property_exists(Transaction::class, $prop)) {
                return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
            }
        }

        if (empty($data['from_id']) || empty($data['to_id'])) {
            return $this->errResponse("Wrong data - user", Response::HTTP_PARTIAL_CONTENT);
        }
        $userFrom = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($data['from_id']);
        $userTo = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($data['to_id']);

        if (empty($userFrom) || empty($userTo)) {
            return $this->errResponse("Wrong data - user", Response::HTTP_PARTIAL_CONTENT);
        }

        $balanceFrom = $this->getDoctrine()
            ->getRepository(Balance::class)
            ->find($userFrom->getAccountId());
        $balanceTo = $this->getDoctrine()
            ->getRepository(Balance::class)
            ->find($userTo->getAccountId());

        if (empty($balanceFrom) || empty($balanceTo)) {
            return $this->errResponse("Wrong data", Response::HTTP_PARTIAL_CONTENT);
        } elseif (!$balanceFrom->isAmountAvailable($data['amount'])) {
            return $this->errResponse("Wrong data - amount", Response::HTTP_PARTIAL_CONTENT);
        }

        $transaction = new Transaction($data);

        if ($transaction->hasNoErr()) {
            (new TransactionService())->executeTransaction($balanceFrom, $balanceTo, $transaction);
        } else {
            return $this->errResponse("Wrong transaction data - transaction ". $transaction->status, Response::HTTP_PARTIAL_CONTENT);
        }

        return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
    }
}