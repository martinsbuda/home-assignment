<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Transaction;

class TransferService
{

    private $entityManager;
    private $httpClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    public function transferFunds(Account $source_account, Account $destination_account, float $amount, Currency $currency): void
    {
        // We only allow to transfer funds when currency is the same as the destination account
        if ($destination_account->getCurrencyId() !== $currency->getId()) {
            throw new \Exception("Currency of funds in transfer operation must match receiver's account currency", Response::HTTP_BAD_REQUEST);
        }

        $exchange_rate = null;

        // If the source account currency is different from the destination account currency, we need to convert the amount
        if ($source_account->getCurrencyId() !== $destination_account->getCurrencyId()) {
            $converted = $this->convertCurrency($amount, $source_account->getCurrency()->getCode(), $destination_account->getCurrency()->getCode());
            $converted_amount = $converted['converted_amount'];
            $exchange_rate = $converted['exchange_rate'];
        } else {
            $converted_amount = $amount;
        }

        // Perform the transfer
        $this->transfer($source_account, $destination_account, $amount, $converted_amount, $exchange_rate);
    }

    private function convertCurrency(float $amount, string $source_currency, string $target_currency): array
    {

        $exchange_api_base_url = $_ENV['EXCHANGE_API_URL'];
        $exchange_access_key = $_ENV['EXCHANGE_API_KEY'];

        // Fetch exchange rate from the currency exchange service
        try {
            $response = $this->httpClient->request('GET', "{$exchange_api_base_url}?access_key={$exchange_access_key}&source={$target_currency}&currencies={$source_currency}");
            $data = $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch exchange rate', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $exchange_rate = $data['quotes'][$target_currency . $source_currency] ?? null;

        if (!$exchange_rate) {
            throw new \Exception('Failed to fetch exchange rate', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return [
            'exchange_rate' => $exchange_rate,
            'converted_amount' => $amount * $exchange_rate
        ];
    }

    private function transfer(Account $source_account, Account $destination_account, float $receiver_amount, float $sender_amount, float $exchange_rate): void
    {
        // Ensure source account has sufficient balance
        if ($source_account->getBalance() < $sender_amount) {
            throw new \Exception('Insufficient balance', Response::HTTP_BAD_REQUEST);
        }

        // Update balances TODO fix, because each account has a different currency
        $source_account->setBalance($source_account->getBalance() - $sender_amount); // use converted amount 
        $destination_account->setBalance($destination_account->getBalance() + $receiver_amount); // use initial amount

        // Create a new Transaction
        $transaction = new Transaction();
        $transaction->setSourceAccountId($source_account->getId());
        $transaction->setDestinationAccountId($destination_account->getId());
        $transaction->setAmount($receiver_amount); // use initial amount
        $transaction->setCurrencyId($destination_account->getCurrencyId()); // use initial currency
        $transaction->setConvertedAmount($sender_amount); // use converted amount
        $transaction->setConvertedCurrencyId($source_account->getCurrencyId()); // use converted currency
        $transaction->setExchangeRate($exchange_rate);
        $transaction->setTransactionDate(new \DateTimeImmutable());
        $transaction->setCreatedAt(new \DateTimeImmutable());

        // Persist the Transaction to the database
        $this->entityManager->persist($transaction);

        // Persist changes to the database
        $this->entityManager->flush();
    }
}
