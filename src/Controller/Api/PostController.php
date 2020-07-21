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
     * @Route("/api/post/", name="api_post", methods={"POST"})
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

        if (!empty($data['from_id'])) {
            $userFrom = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($data['from_id']);
        }
        if (!empty($data['to_id'])) {
            $userTo = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($data['to_id']);
        }

        if (isset($userFrom) && isset($userTo)) {
            $balanceFrom = $this->getDoctrine()
                ->getRepository(Balance::class)
                ->find($userFrom->getAccountId());
            $balanceTo = $this->getDoctrine()
                ->getRepository(Balance::class)
                ->find($userTo->getAccountId());
            if (isset($balanceFrom) && isset($balanceTo)) {
                $transaction = new Transaction($data);
            } else {
                return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
            }
        } else {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }

        if ($transaction->hasNoErr()) {
            (new TransactionService())->executeTransaction($balanceFrom, $balanceTo, $transaction);
        } else {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }

        return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
    }
}