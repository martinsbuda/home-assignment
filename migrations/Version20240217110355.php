<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240217110355 extends AbstractMigration
{

    // This migration was made for PostgreSQL, so the syntax may differ for other database systems
    public function up(Schema $schema): void
    {
        // Create Client table
        $this->addSql('CREATE TABLE client (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )');

        // Create Currency table
        $this->addSql('CREATE TABLE currency (
            id SERIAL PRIMARY KEY,
            code VARCHAR(3) NOT NULL,
            name VARCHAR(255) NOT NULL
        )');

        // Create Account table
        $this->addSql('CREATE TABLE account (
            id SERIAL PRIMARY KEY,
            client_id INT NOT NULL,
            currency_id INT NOT NULL,
            balance FLOAT NOT NULL,
            CONSTRAINT FK_client FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE,
            CONSTRAINT FK_currency FOREIGN KEY (currency_id) REFERENCES currency (id) ON DELETE CASCADE
        )');

        // Create Transaction table
        $this->addSql('CREATE TABLE transaction (
            id SERIAL PRIMARY KEY,
            source_account_id INT NOT NULL,
            destination_account_id INT NOT NULL,
            amount FLOAT NOT NULL,
            currency_id INT NOT NULL,
            exchange_rate FLOAT,
            converted_amount FLOAT,
            converted_currency_id INT,
            transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP,
            CONSTRAINT FK_source_account FOREIGN KEY (source_account_id) REFERENCES account (id) ON DELETE CASCADE,
            CONSTRAINT FK_destination_account FOREIGN KEY (destination_account_id) REFERENCES account (id) ON DELETE CASCADE,
            CONSTRAINT FK_currency FOREIGN KEY (currency_id) REFERENCES currency (id) ON DELETE CASCADE
        )');
    }

    public function down(Schema $schema): void
    {
        // Drop tables if the migration is rolled back
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE client');
    }
}
