<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Currency;
use App\Entity\Transaction;

class TransactionFixture extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            AccountFixture::class
        ];
    }

    public function load(ObjectManager $manager)
    {

        $currency = [
            'USD' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'USD'])->getId(),
            'EUR' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'EUR'])->getId(),
            'GBP' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'GBP'])->getId(),
            'PLN' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'PLN'])->getId(),
            'JPY' => $manager->getRepository(Currency::class)->findOneBy(['code' => 'JPY'])->getId()
        ];

        // Create dummy transactions and persist them to the database

        // 10.00 GBP from USD Account=1 to GBP Account=3
        $transaction = new Transaction();
        $transaction->setSourceAccountId(1);
        $transaction->setDestinationAccountId(3);
        $transaction->setAmount(10.00);
        $transaction->setCurrencyId($currency['GBP']);
        $transaction->setExchangeRate(1.25972);
        $transaction->setConvertedAmount(12.597199);
        $transaction->setConvertedCurrencyId($currency['USD']);
        $transaction->setTransactionDate(new \DateTimeImmutable('2024-02-18 13:18:31'));
        $transaction->setCreatedAt(new \DateTimeImmutable('2024-02-18 13:18:31'));
        $manager->persist($transaction);

        // 4.35 GBP from USD Account=1 to GBP Account=3
        $transaction = new Transaction();
        $transaction->setSourceAccountId(1);
        $transaction->setDestinationAccountId(3);
        $transaction->setAmount(4.35);
        $transaction->setCurrencyId($currency['GBP']);
        $transaction->setExchangeRate(1.25972);
        $transaction->setConvertedAmount(5.482548);
        $transaction->setConvertedCurrencyId($currency['USD']);
        $transaction->setTransactionDate(new \DateTimeImmutable('2024-02-18 14:24:11'));
        $transaction->setCreatedAt(new \DateTimeImmutable('2024-02-18 14:24:11'));
        $manager->persist($transaction);

        // 100.00 USD from GBP Account=3 to USD Account=1
        $transaction = new Transaction();
        $transaction->setSourceAccountId(3);
        $transaction->setDestinationAccountId(1);
        $transaction->setAmount(100.00);
        $transaction->setCurrencyId($currency['USD']);
        $transaction->setExchangeRate(0.79381);
        $transaction->setConvertedAmount(79.381);
        $transaction->setConvertedCurrencyId($currency['GBP']);
        $transaction->setTransactionDate(new \DateTimeImmutable('2024-02-18 16:19:01'));
        $transaction->setCreatedAt(new \DateTimeImmutable('2024-02-18 16:19:01'));
        $manager->persist($transaction);


        // Flush changes
        $manager->flush();
    }
}
