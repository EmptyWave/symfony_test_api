<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\Transaction;
use App\Entity\Balance;
use App\Entity\User;

class TransactionsTest extends TestCase
{
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testSuccessTransaction()
    {
        $httpClient = HttpClient::create();

        $idFrom = 1;
        $idTo = 2;
        $amount = 500;

        $response = $httpClient->request('POST', "http://api-symf/api/?from_id={$idFrom}&to_id={$idTo}&amount={$amount}");

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals(Transaction::SUCCESS, $data['status']);
        $this->assertArrayHasKey('created', $data);
        $this->assertArrayHasKey('idTransaction', $data);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testFailedTransaction()
    {
        $httpClient = HttpClient::create();

        $idFrom = 1;
        $idTo = 666;
        $amount = 500;

        $response = $httpClient->request('POST', "http://api-symf/api/?from={$idFrom}&to={$idTo}&amount={$amount}");
        $this->assertEquals(206, $response->getStatusCode());
        $this->assertEquals('Wrong transaction data', $response->getContent());
    }

}