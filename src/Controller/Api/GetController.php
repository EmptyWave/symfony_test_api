<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\{Balance, User};
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\{Request, Response};
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class GetController extends BaseApiController
{
    /**
     * @Route("/api/", name="api_get", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $method = $request->query->get('method');

        switch ($method) {
            case 'transaction':
                return $this->transactionAction($request);
            case 'balance':
                return $this->balanceAction($request);
            default:
                return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function transactionAction(Request $request): Response
    {
        $transactionId = $request->query->get('transaction_id');

        if (empty($transactionId)) {
            return $this->errResponse("Wrong transaction data", Response::HTTP_PARTIAL_CONTENT);
        }

        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->find($transactionId);

        if ($transaction) {
            return $this->okResponse($transaction->getDataArray(), Response::HTTP_OK);
        } else {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function balanceAction(Request $request): Response
    {
        $userId = $request->query->get('user_id');

        if (empty($userId)) {
            return $this->errResponse("Wrong user data", Response::HTTP_PARTIAL_CONTENT);
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        if (!$user) {
            return $this->errResponse("No user data", Response::HTTP_NO_CONTENT);
        }

        $balance = $this->getDoctrine()
            ->getRepository(Balance::class)
            ->find($user->getAccountId());

        if ($balance) {
            return $this->okResponse($balance->getDataArray(), Response::HTTP_OK);
        } else {
            return $this->errResponse("No result", Response::HTTP_NO_CONTENT);
        }

    }
}