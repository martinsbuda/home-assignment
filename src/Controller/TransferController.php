<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\TransferService;
use App\Repository\AccountRepository;
use App\Repository\CurrencyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransferController extends AbstractController
{
    private $transferService;
    private $accountRepository;
    private $currencyRepository;

    public function __construct(TransferService $transferService, AccountRepository $accountRepository, CurrencyRepository $currencyRepository)
    {
        $this->transferService = $transferService;
        $this->accountRepository = $accountRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function transferFunds(Request $request): Response
    {
        $request_data = $request->request->all();

        if (!$this->validateRequest($request_data)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid input data'
            ], Response::HTTP_BAD_REQUEST);
        }

        $source_account_id = $request_data['sourceAccountId'];
        $destination_account_id = $request_data['destinationAccountId'];
        $amount = $request_data['amount'];
        $currency = $request_data['currency'];

        // Retrieve source and destination account entities from the database
        $source_account = $this->accountRepository->find($source_account_id);
        $destination_account = $this->accountRepository->find($destination_account_id);
        $currency = $this->currencyRepository->findOneBy(['code' => $currency]);

        // Validate source and destination accounts
        if (!$source_account || !$destination_account) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid source or destination account'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$currency) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid currency'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Perform the fund transfer
        try {
            $this->transferService->transferFunds($source_account, $destination_account, $amount, $currency);

            return new JsonResponse([
                'success' => true,
                'message' => 'Funds transferred successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    private function validateRequest($request)
    {

        // Check if the request contains the required parameters
        if (!isset($request['sourceAccountId'], $request['destinationAccountId'], $request['amount'], $request['currency'])) {
            return false;
        }

        // Check if required parameters are numeric
        if (!is_numeric($request['sourceAccountId']) || !is_numeric($request['destinationAccountId']) || !is_numeric($request['amount'])) {
            return false;
        }

        // Check if the amount is greater than 0
        if ($request['amount'] <= 0) {
            return false;
        }

        return true;
    }
}
