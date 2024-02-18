<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{

    /**
    Given a client ID, return a list of accounts
        Scenario 1: Client has multiple accounts.
            Test 1: Test that the API returns all accounts associated with the given client ID.
            Test 2: Test that the API returns the correct number of accounts for the client.
            Test 3: Test that the API returns the correct account details, including currency and balance.
        
        Scenario 2: Client has one account.
            Test 4: Test that the API returns the single account associated with the given client ID.
            Test 5: Test that the API returns the correct account details, including currency and balance.
        
        Scenario 3: Client has no accounts.
            Test 6: Test that the API returns an empty array when the client has no accounts.
        
        Scenario 4: Client does not exist.
            Test 7: Test that the API returns an error when the client does not exist.
        
        Scenario 5: Client ID is not provided.
            Test 8: Test that the API returns an error when the client ID is not provided.
     */

    // Test 1: Test that the API returns all accounts associated with the given client ID.
    public function testGetAccountsByClientIdWithExistingAccounts()
    {
        $client = static::createClient();

        $client->request('GET', '/client/1/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertNotEmpty($responseData['data']);
        // Add more assertions to verify the structure and content of the response data
    }

    // Test 2: Test that the API returns the correct number of accounts for the client.
    public function testGetAccountsByClientIdWithCorrectNumberOfAccounts()
    {
        $client = static::createClient();

        $client->request('GET', '/client/1/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(2, $responseData['data']);
    }

    // Test 3: Test that the API returns the correct account details, including currency and balance.
    public function testGetAccountsByClientIdWithCorrectAccountDetails()
    {
        $client = static::createClient();

        $client->request('GET', '/client/1/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('id', $responseData['data'][0]);
        $this->assertArrayHasKey('client_id', $responseData['data'][0]);
        $this->assertArrayHasKey('currency', $responseData['data'][0]);
        $this->assertArrayHasKey('balance', $responseData['data'][0]);

        // Check for specific values
        $this->assertEquals('1', $responseData['data'][0]['id']);
        $this->assertEquals('1', $responseData['data'][0]['client_id']);
        $this->assertEquals('USD', $responseData['data'][0]['currency']);
        $this->assertEquals(1000, $responseData['data'][0]['balance']);

        $this->assertEquals('2', $responseData['data'][1]['id']);
        $this->assertEquals('1', $responseData['data'][1]['client_id']);
        $this->assertEquals('EUR', $responseData['data'][1]['currency']);
        $this->assertEquals(500, $responseData['data'][1]['balance']);
    }

    // Test 4: Test that the API returns the single account associated with the given client ID.
    public function testGetAccountsByClientIdWithSingleAccount()
    {
        $client = static::createClient();

        $client->request('GET', '/client/2/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(1, $responseData['data']);
    }

    // Test 5: Test that the API returns the correct account details, including currency and balance.
    public function testGetAccountsByClientIdWithSingleAccountDetails()
    {
        $client = static::createClient();

        $client->request('GET', '/client/2/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('id', $responseData['data'][0]);
        $this->assertArrayHasKey('client_id', $responseData['data'][0]);
        $this->assertArrayHasKey('currency', $responseData['data'][0]);
        $this->assertArrayHasKey('balance', $responseData['data'][0]);

        // Check for specific values
        $this->assertEquals('3', $responseData['data'][0]['id']);
        $this->assertEquals('2', $responseData['data'][0]['client_id']);
        $this->assertEquals('GBP', $responseData['data'][0]['currency']);
        $this->assertEquals(750, $responseData['data'][0]['balance']);
    }

    // Test 6: Test that the API returns an empty array when the client has no accounts.
    public function testGetAccountsByClientIdWithNoAccounts()
    {
        $client = static::createClient();

        $client->request('GET', '/client/6/accounts');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertEmpty($responseData['data']);
    }

    // Test 7: Test that the API returns an error when the client does not exist.
    public function testGetAccountsByClientIdWithNonExistentClient()
    {
        $client = static::createClient();

        $client->request('GET', '/client/10/accounts');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals('Client not found', $responseData['error']);
    }

    // Test 8: Test that the API returns an error when the client ID is not provided.
    public function testGetAccountsByClientIdWithoutClientId()
    {
        $client = static::createClient();

        $client->request('GET', '/client//accounts');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
