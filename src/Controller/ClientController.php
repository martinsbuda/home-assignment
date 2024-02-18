<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AccountRepository;

class ClientController extends AbstractController
{
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getAccountsByClientId($client_id): Response
    {

        // Return an error response if the client is not found
        $client = $this->accountRepository->find($client_id);
        if (empty($client)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Client not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Retrieve accounts associated with the given client ID
        $accounts = $this->accountRepository->findByClientId($client_id);

        // Return an error response if no accounts are found
        if (empty($accounts)) {
            return new JsonResponse([
                'success' => true,
                'data' => []
            ]);
        }


        $response_data = [];
        foreach ($accounts as $account) {
            $response_data[] = [
                'id' => $account->getId(),
                'client_id' => $account->getClientId(),
                'currency' => $account->getCurrency()->getCode(),
                'balance' => $account->getBalance(),
            ];
        }

        // Return response
        return new JsonResponse([
            'success'   => true,
            'data'  => $response_data
        ]);
    }
}
