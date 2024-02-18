<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;

class ClientFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create dummy clients and persist them to the database
        $client = new Client();

        $client->setName('John Doe');
        $manager->persist($client);

        $client = new Client();
        $client->setName('Jane Smith');
        $manager->persist($client);

        $client = new Client();
        $client->setName('Ethan Smith');
        $manager->persist($client);

        $client = new Client();
        $client->setName('Emma Jones');
        $manager->persist($client);

        $client = new Client();
        $client->setName('Liam Williams');
        $manager->persist($client);

        // Flush changes
        $manager->flush();
    }
}
