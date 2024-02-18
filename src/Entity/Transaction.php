<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $source_account_id = null;

    #[ORM\Column]
    private ?int $destination_account_id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?int $currency_id = null;

    #[ORM\Column(nullable: true)]
    private ?float $exchange_rate = null;

    #[ORM\Column(nullable: true)]
    private ?float $converted_amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $converted_currency_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $transaction_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceAccountId(): ?int
    {
        return $this->source_account_id;
    }

    public function setSourceAccountId(int $source_account_id): static
    {
        $this->source_account_id = $source_account_id;

        return $this;
    }

    public function getDestinationAccountId(): ?int
    {
        return $this->destination_account_id;
    }

    public function setDestinationAccountId(int $destination_account_id): static
    {
        $this->destination_account_id = $destination_account_id;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyId(): ?int
    {
        return $this->currency_id;
    }

    public function setCurrencyId(int $currency_id): static
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    public function getExchangeRate(): ?float
    {
        return $this->exchange_rate;
    }

    public function setExchangeRate(?float $exchange_rate): static
    {
        $this->exchange_rate = $exchange_rate;

        return $this;
    }

    public function getConvertedAmount(): ?float
    {
        return $this->converted_amount;
    }

    public function setConvertedAmount(?float $converted_amount): static
    {
        $this->converted_amount = $converted_amount;

        return $this;
    }

    public function getConvertedCurrencyId(): ?int
    {
        return $this->converted_currency_id;
    }

    public function setConvertedCurrencyId(?int $converted_currency_id): static
    {
        $this->converted_currency_id = $converted_currency_id;

        return $this;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transaction_date;
    }

    public function setTransactionDate(\DateTimeInterface $transaction_date): static
    {
        $this->transaction_date = $transaction_date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
