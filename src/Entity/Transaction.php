<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use App\Service\MyEntityManager;
use DateTime;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @Note Transaction statuses.
     */
    const SUCCESS_CODE = 1;
    const ERR_CODE = 2;

    const SUCCESS = 10;
    const SUCCESS_CREATE = 11;
    const ERR_SENDER = 20;
    const ERR_RECIPIENT = 21;
    const ERR_AMOUNT = 22;
    const ERR_BD = 23;
    const CANCELED = 30;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $to_id;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $status;

    /**
     * @ORM\Column(type="datetime", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private $created;

    /**
     * Transaction constructor.
     * @param $data
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __construct($data)
    {
        $this->setFromId((int)$data['from_id'])
            ->setToId((int)$data['to_id'])
            ->setAmount($data['amount'])
            ->setCreated();
        $this->validateData();
        $this->create();
    }

    /**
     * validator
     */
    private function validateData()
    {
        $this->validateFrom();
        $this->validateTo();
        $this->validateAmount();
        if (empty($this->status)) {
            $this->setStatus(self::SUCCESS_CREATE);
        }
    }

    /**
     * validate user SENDER data
     */
    private function validateFrom()
    {
        if (empty($this->from_id) || !(is_integer($this->from_id))) {
            if (empty($this->status)) {
                $this->setStatus(self::ERR_SENDER);
            }
        }
    }

    /**
     * validate user RECIPIENT data
     */
    private function validateTo()
    {
        if (empty($this->to_id) || !(is_integer($this->from_id))) {
            if (empty($this->status)) {
                $this->setStatus(self::ERR_RECIPIENT);
            }
        }
    }

    /**
     * validate amount data
     */
    private function validateAmount()
    {
        if (empty($this->amount) || ($this->amount <= 0)) {
            if (empty($this->status)) {
                $this->setStatus(self::ERR_AMOUNT);
            }
        }
    }

    /**
     * @return array
     */
    public function getDataArray(): array
    {
        $data = [];
        $data['idTransaction'] = $this->getId();
        $data['created'] = $this->getCreated();
        $data['status'] = $this->getStatus();
        return $data;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getFromId(): ?int
    {
        return $this->from_id;
    }

    /**
     * @return null|string
     */
    public function getCreated(): ?datetime
    {
        return $this->created;
    }

    /**
     * @param int $from_id
     * @return Transaction
     */
    public function setFromId(int $from_id): self
    {
        $this->from_id = $from_id;

        return $this;
    }

    /**
     * @param
     * @return Transaction
     */
    public function setCreated(): self
    {
        $this->created = new DateTime(date("Y-m-d H:i:s"));

        return $this;
    }

    /**
     * @return int|null
     */
    public function getToId(): ?int
    {
        return $this->to_id;
    }

    /**
     * @param int $to_id
     * @return Transaction
     */
    public function setToId(int $to_id): self
    {
        $this->to_id = $to_id;

        return $this;
    }

    /**
     * @return null|float
     */
    public function getAmount(): ?float
    {
        return (float)$this->amount;
    }

    /**
     * @param string $amount
     * @return Transaction
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Transaction
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
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
     * @return bool
     */
    public function hasNoErr(): bool
    {
        if (intval($this->status / 10) == static::SUCCESS_CODE) {
            return true;
        }
        return false;
    }

}
