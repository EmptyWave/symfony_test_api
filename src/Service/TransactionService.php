<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Balance;
use App\Entity\Transaction;

class TransactionService
{
    /**
     * @param Balance $balanceFrom
     * @param Balance $balanceTo
     * @param Transaction $transaction
     * @return Transaction
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function executeTransaction(Balance $balanceFrom, Balance $balanceTo, Transaction $transaction)
    {
        $amount = $transaction->getAmount();
        $em = MyEntityManager::get();
        $conn = $em->getConnection();
        $conn->setAutoCommit(false);
        $conn->beginTransaction();

        try {
            $balanceFrom->updateBalance($amount, Balance::OPERATION_TAKE);
            $balanceFrom->update();

            $balanceTo->updateBalance($amount, Balance::OPERATION_ADD);
            $balanceTo->update();

            $transaction->setStatus(Transaction::SUCCESS);
            $transaction->update();

            $conn->commit();

        } catch (\Exception $e) {
            $conn->rollBack();

            $transaction->setStatus(Transaction::ERR_BD);

            $em->flush();
        }
        return $transaction;
    }
}