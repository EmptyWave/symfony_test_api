<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Service\MyEntityManager;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BalanceRepository")
 */
class Balance
{

    const OPERATION_ADD = 'add';

    const OPERATION_TAKE = 'take';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $balance;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|float
     */
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    /**
     * @param float $value
     * @param string $operation
     * @return Balance|null
     * @throws Exception
     */
    public function updateBalance(float $value, string $operation): ?self
    {
        if ($operation == static::OPERATION_TAKE) {
            if (!$this->isNegative($value)) {
                $this->balance -= $value;
                return $this;
            } else {
                throw new Exception('Transfer not possible, insufficient funds');
            }
        } elseif ($operation == static::OPERATION_ADD) {
            $this->balance += $value;
            return $this;
        }
        throw new Exception('Transfer err, incorrect operation');
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    private function create()
    {
        $em = MyEntityManager::get();
        try {
            $em->persist($this);
            $em->flush();
        } catch (Exception $e) {

        }
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function update()
    {
        $em = MyEntityManager::get();
        try {
            $em->merge($this);
            $em->flush();
        } catch (Exception $e) {

        }
    }

    /**
     * @param $value
     * @return bool
     */
    public function isNegative($value): bool
    {
        return ($this->balance - $value) <= 0;
    }
}
