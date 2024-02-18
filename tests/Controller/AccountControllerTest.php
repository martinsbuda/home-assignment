<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{

    /**
    Given an account ID, return transaction history with paging:
        Scenario 1: Account has transaction history.
            Test 1: Test that the API returns transaction history for the given account ID.
            Test 2: Test that the API supports result paging using offset and limit parameters.
            Test 3: Test that the API returns the correct number of transactions based on the paging parameters.
            Test 4: Test that the API returns outgoing and incoming transactions.
        Scenario 2: Account has no transaction history.
            Test 5: Test that the API returns an empty array when the account has no transaction history.
        Scenario 3: Account does not exist.
            Test 6: Test that the API returns an error when the account does not exist.
        Scenario 4: Account ID is not provided.
            Test 7: Test that the API returns an error when the account ID is not provided.
     */

    // Test 1: Test that the API returns transaction history for the given account ID.
    public function testGetTransactionsByAccountIdWithExistingTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/account/1/transactions');
        $client->followRedirects(true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertNotEmpty($responseData['data']);
        $this->assertCount(3, $responseData['data']);
    }

    // Test 2: Test that the API supports result paging using limit parameter.
    public function testGetTransactionsByAccountIdWithPaging()
    {
        $client = static::createClient();

        $client->request('GET', '/account/1/transactions?limit=2');
        $client->followRedirects(true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(2, $responseData['data']);
    }

    // Test 3: Test that the API returns the correct number of transactions based on the paging parameters.
    public function testGetTransactionsByAccountIdWithCorrectNumberOfTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/account/1/transactions?offset=1&limit=2');
        $client->followRedirects(true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(2, $responseData['data']);
    }

    // Test 4: Test that the API returns outgoing and incoming transactions.
    public function testGetTransactionsByAccountIdWithOutgoingAndIncomingTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/account/1/transactions');
        $client->followRedirects(true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(3, $responseData['data']);
        $this->assertEquals(3, $responseData['data'][0]['source_account_id']);
        $this->assertEquals(1, $responseData['data'][0]['destination_account_id']);
        $this->assertEquals(1, $responseData['data'][1]['source_account_id']);
        $this->assertEquals(3, $responseData['data'][1]['destination_account_id']);
    }

    // Test 5: Test that the API returns an empty array when the account has no transaction history.
    public function testGetTransactionsByAccountIdWithNoTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/account/4/transactions');
        $client->followRedirects(true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertEmpty($responseData['data']);
    }

    // Test 6: Test that the API returns an error when the account does not exist.
    public function testGetTransactionsByAccountIdWithNonExistentAccount()
    {
        $client = static::createClient();

        $client->request('GET', '/account/10/transactions');
        $client->followRedirects(true);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals('Account not found', $responseData['error']);
    }

    // Test 7: Test that the API returns an error when the account ID is not provided.
    public function testGetTransactionsByAccountIdWithNoAccountId()
    {
        $client = static::createClient();

        $client->request('GET', '/account//transactions');
        $client->followRedirects(true);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
