<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Account;
use App\Entity\Client;
use App\Entity\Currency;

class AccountFixture extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            ClientFixture::class,
            CurrencyFixture::class
        ];
    }

    public function load(ObjectManager $manager)
    {

        $currency = [
            'USD' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'USD']),
            'EUR' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'EUR']),
            'GBP' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'GBP']),
            'PLN' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'PLN']),
            'JPY' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'JPY'])
        ];

        // Create dummy accounts for the client and persist them to the database
        $client = $manager->getRepository(Client::class)->findOneBy(['name' => 'John Doe']);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['USD']);
        $account->setBalance(1000.00);
        $manager->persist($account);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['EUR']);
        $account->setBalance(500.00);
        $manager->persist($account);


        // Another account for another client
        $client = $manager->getRepository(Client::class)->findOneBy(['name' => 'Jane Smith']);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['GBP']);
        $account->setBalance(750.00);
        $manager->persist($account);


        // Another account for another client
        $client = $manager->getRepository(Client::class)->findOneBy(['name' => 'Ethan Smith']);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['PLN']);
        $account->setBalance(50.00);
        $manager->persist($account);


        // Another account for another client
        $client = $manager->getRepository(Client::class)->findOneBy(['name' => 'Emma Jones']);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['JPY']);
        $account->setBalance(1500.00);
        $manager->persist($account);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['USD']);
        $account->setBalance(350.00);
        $manager->persist($account);


        // Another account for another client
        $client = $manager->getRepository(Client::class)->findOneBy(['name' => 'Liam Williams']);

        $account = new Account();
        $account->setClientId($client->getId());
        $account->setCurrency($currency['EUR']);
        $account->setBalance(1000.00);
        $manager->persist($account);

        // Flush changes
        $manager->flush();
    }
}
