<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends AbstractController
{
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function getTransactionsByAccountId(Request $request, $account_id): Response
    {
        // Retrieve offset and limit from request
        $offset = $request->query->get('offset', 0); // Default offset is 0
        $limit = $request->query->get('limit', 10); // Default limit is 10

        // Retrieve transaction history for the given account ID with offset and limit
        $transactions = $this->transactionRepository->findByAccountId($account_id, $offset, $limit);

        // Prepare response data
        $responseData = [];
        foreach ($transactions as $transaction) {
            $responseData[] = [
                'id' => $transaction->getId(),
                'source_account_id' => $transaction->getSourceAccountId(),
                'destination_account_id' => $transaction->getDestinationAccountId(),
                'amount' => $transaction->getAmount(),
                'currency_id' => $transaction->getCurrencyId(),
                'exchange_rate' => $transaction->getExchangeRate(),
                'converted_amount' => $transaction->getConvertedAmount(),
                'converted_currency_id' => $transaction->getConvertedCurrencyId(),
                'transaction_date' => $transaction->getTransactionDate()->format('Y-m-d H:i:s'),
            ];
        }

        // Return response
        return new JsonResponse([
            'success' => true,
            'data' => $responseData,
            'meta' => [
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => count($transactions),
            ]
        ]);
    }
}
