<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransferControllerTest extends WebTestCase
{

     /**
     Transfer funds between two accounts:
          Scenario 1: Transfer with same curency.
              Test 1: Test that the API returns a successful response when transferring funds between two accounts with the same currency.
              Test 2: Test that the API returns the correct balance for the source and destination accounts after the transfer.
              Test 3: Test that the API returns the correct transaction details after the transfer.
          Scenario 2: Transfer with different currency.
               Test 4: Test that the API returns a successful response when transferring funds between two accounts with different currencies.
               Test 5: Test that the API returns the correct transaction details after the transfer.
          Scenario 3: Insufficient funds.
               Test 6: Test that the API returns an error when the source account has insufficient funds.
          Scenario 4: Source account does not exist.
               Test 7: Test that the API returns an error when the source account does not exist.
          Scenario 5: Destination account does not exist.
               Test 8: Test that the API returns an error when the destination account does not exist.
          Scenario 6: Source account and destination account are the same.
               Test 9: Test that the API returns an error when the source account and destination account are the same.
          Scenario 7: Source account ID is not provided.
               Test 10: Test that the API returns an error when the source account ID is not provided.
          Scenario 8: Destination account ID is not provided.
               Test 11: Test that the API returns an error when the destination account ID is not provided.
          Scenario 9: Amount is not provided.
               Test 12: Test that the API returns an error when the amount is not provided.
          Scenario 10: Amoint is 0
               Test 13: Test that the API returns an error when the amount is 0.
          Scenario 10: Currency is not provided.
               Test 14: Test that the API returns an error when the currency ID is not provided.
          Scenario 11: Currency does not exist.
               Test 15: Test that the API returns an error when the currency does not exist.
     */

     // Test 1: Test that the API returns a successful response when transferring funds between two accounts with the same currency.
     public function testTransferFundsBetweenAccountsWithSameCurrency()
     {
         $client = static::createClient();

         $client->request('POST', '/transfer', [
             'sourceAccountId' => 2,
             'destinationAccountId' => 7,
             'amount' => 10.00,
             'currency' => 'EUR'
         ]);

         $this->assertEquals(200, $client->getResponse()->getStatusCode());

         $responseData = json_decode($client->getResponse()->getContent(), true);

         $this->assertTrue($responseData['success']);
     }

     // Test 2: Test that the API returns the correct balance for the source and destination accounts after the transfer.
     public function testTransferFundsBetweenAccountsWithSameCurrencyBalance()
     {
          // Get balance of source and destination accounts
          $client = static::createClient();
          $client->request('GET', '/client/1/accounts');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          $balance_before_transfer_source = $responseData['data'][1]['balance'];

          $client->request('GET', '/client/5/accounts');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          $balance_before_transfer_destination = $responseData['data'][0]['balance'];

          // Transfer funds
          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 15.00,
              'currency' => 'EUR'
          ]);

          // Get balance of source and destination accounts
          $client->request('GET', '/client/1/accounts');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          $balance_after_transfer_source = $responseData['data'][1]['balance'];

          $client->request('GET', '/client/5/accounts');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          $balance_after_transfer_destination = $responseData['data'][0]['balance'];

          $this->assertEquals($balance_before_transfer_source - 15.00, $balance_after_transfer_source);
          $this->assertEquals($balance_before_transfer_destination + 15.00, $balance_after_transfer_destination);
     }

     // Test 3: Test that the API returns the correct transaction details after the transfer.
     public function testTransferFundsBetweenAccountsWithSameCurrencyTransactionDetails()
     {
          // Transfer funds
          $client = static::createClient();
          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 12.34,
              'currency' => 'EUR'
          ]);

          // Get transaction details
          $client->request('GET', '/account/2/transactions');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          
          $this->assertEquals(2, $responseData['data'][0]['source_account_id']);
          $this->assertEquals(7, $responseData['data'][0]['destination_account_id']);
          $this->assertEquals(12.34, $responseData['data'][0]['amount']);
          $this->assertEquals(47, $responseData['data'][0]['currency_id']); // Currency ID for EUR
          $this->assertNull($responseData['data'][0]['exchange_rate']);
          $this->assertNull($responseData['data'][0]['converted_amount']);
          $this->assertNull($responseData['data'][0]['converted_currency_id']);
     }

     // Test 4: Test that the API returns a successful response when transferring funds between two accounts with different currencies.
     public function testTransferFundsBetweenAccountsWithDifferentCurrency()
     {
         $client = static::createClient();

         $client->request('POST', '/transfer', [
             'sourceAccountId' => 7,
             'destinationAccountId' => 6,
             'amount' => 5.50,
             'currency' => 'USD'
         ]);

         $this->assertEquals(200, $client->getResponse()->getStatusCode());

         $responseData = json_decode($client->getResponse()->getContent(), true);

         $this->assertTrue($responseData['success']);
     }

     // Test 5: Test that the API returns the correct transaction details after the transfer.
     public function testTransferFundsBetweenAccountsWithDifferentCurrencyTransactionDetails()
     {
          // Transfer funds
          $client = static::createClient();
          $client->request('POST', '/transfer', [
              'sourceAccountId' => 7,
              'destinationAccountId' => 6,
              'amount' => 12.34,
              'currency' => 'USD'
          ]);

          // Get transaction details
          $client->request('GET', '/account/7/transactions');
          $responseData = json_decode($client->getResponse()->getContent(), true);
          
          $this->assertEquals(7, $responseData['data'][0]['source_account_id']);
          $this->assertEquals(6, $responseData['data'][0]['destination_account_id']);
          $this->assertEquals(12.34, $responseData['data'][0]['amount']);
          $this->assertEquals(151, $responseData['data'][0]['currency_id']); // Currency ID for USD
          $this->assertNotNull($responseData['data'][0]['exchange_rate']);
          $this->assertNotNull($responseData['data'][0]['converted_amount']);
          $this->assertNotNull($responseData['data'][0]['converted_currency_id']);
     }

     // Test 6: Test that the API returns an error when the source account has insufficient funds.
     public function testTransferFundsBetweenAccountsWithInsufficientFunds() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 10000.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Insufficient balance', $responseData['error']);
     }

     // Test 7: Test that the API returns an error when the source account does not exist.
     public function testTransferFundsBetweenAccountsWithSourceAccountDoesNotExist() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 100,
              'destinationAccountId' => 7,
              'amount' => 100.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid source or destination account', $responseData['error']);
     }

     // Test 8: Test that the API returns an error when the destination account does not exist.
     public function testTransferFundsBetweenAccountsWithDestinationAccountDoesNotExist() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 100,
              'amount' => 100.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid source or destination account', $responseData['error']);
     }

     // Test 9: Test that the API returns an error when the source account and destination account are the same.
     public function testTransferFundsBetweenAccountsWithSourceAndDestinationAccountAreTheSame() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 2,
              'amount' => 100.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 10: Test that the API returns an error when the source account ID is not provided.
     public function testTransferFundsBetweenAccountsWithSourceAccountIdNotProvided() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'destinationAccountId' => 7,
              'amount' => 100.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 11: Test that the API returns an error when the destination account ID is not provided.
     public function testTransferFundsBetweenAccountsWithDestinationAccountIdNotProvided() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'amount' => 100.00,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 12: Test that the API returns an error when the amount is not provided.
     public function testTransferFundsBetweenAccountsWithAmountNotProvided() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 13: Test that the API returns an error when the amount is 0.
     public function testTransferFundsBetweenAccountsWithAmountIs0() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 0,
              'currency' => 'EUR'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 14: Test that the API returns an error when the currency is not provided.
     public function testTransferFundsBetweenAccountsWithCurrencyNotProvided() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 100.00
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid input data', $responseData['error']);
     }

     // Test 15: Test that the API returns an error when the currency does not exist.
     public function testTransferFundsBetweenAccountsWithCurrencyDoesNotExist() {
          $client = static::createClient();

          $client->request('POST', '/transfer', [
              'sourceAccountId' => 2,
              'destinationAccountId' => 7,
              'amount' => 100.00,
              'currency' => 'XYZ'
          ]);

          $this->assertEquals(400, $client->getResponse()->getStatusCode());

          $responseData = json_decode($client->getResponse()->getContent(), true);

          $this->assertFalse($responseData['success']);
          $this->assertEquals('Invalid currency', $responseData['error']);
     }
}