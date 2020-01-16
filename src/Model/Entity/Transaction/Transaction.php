<?php declare(strict_types = 1);

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\Entity\EntityInterface;

/** @codeCoverageIgnore  */
class Transaction implements EntityInterface
{
    /**
     * @var int
     */
    protected $entityId = 0;
    
    /**
     * @var int
     */
    protected $userId;
    
    /**
     * @var string
     */
    protected $transactionStatus;
    
    /**
     * @var float
     */
    protected $transactionFee;
    
    /**
     * @var string
     */
    protected $createdAt;
    
    /**
     * @var string
     */
    protected $updatedAt;
    
    /**
     * @var string
     */
    protected $transactionProvider;
    
    /**
     * @var float
     */
    protected $transactionAmount;
    
    /**
     * @var int
     */
    protected $transactionRecipientId;
    
    /**
     * @var string
     */
    protected $transactionRecipientName;
    
    /**
     * @var string
     */
    protected $transactionCurrency;
    
    /**
     * @var string
     */
    protected $transactionDetails;
    
    /**
     * Returns the entity's ID (primary key).
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }
    
    /**
     * Returns the time when entity was created.
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    
    /**
     * Returns the time when the entity was last updated.
     *
     * @return mixed
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getTransactionStatus(): string
    {
        return $this->transactionStatus;
    }
    
    /**
     * @param string $transactionStatus
     *
     * @return Transaction
     */
    public function setTransactionStatus(string $transactionStatus): Transaction
    {
        $this->transactionStatus = $transactionStatus;
        
        return $this;
    }
    
    /**
     * @return float
     */
    public function getTransactionFee(): float
    {
        return $this->transactionFee;
    }
    
    /**
     * @return string
     */
    public function getTransactionProvider(): string
    {
        return $this->transactionProvider;
    }
    
    /**
     * @return float
     */
    public function getTransactionAmount(): float
    {
        return $this->transactionAmount;
    }
    
    /**
     * @return int
     */
    public function getTransactionRecipientId(): int
    {
        return $this->transactionRecipientId;
    }
    
    /**
     * @return string
     */
    public function getTransactionRecipientName(): string
    {
        return $this->transactionRecipientName;
    }
    
    /**
     * @return string
     */
    public function getTransactionCurrency(): string
    {
        return $this->transactionCurrency;
    }
    
    /**
     * @param int $transactionId
     *
     * @return Transaction
     */
    public function setTransactionId(int $transactionId): Transaction
    {
        $this->entityId = $transactionId;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->entityId;
    }
    
    /**
     * @param int $userId
     *
     * @return Transaction
     */
    public function setUserId(int $userId): Transaction
    {
        $this->userId = $userId;
        
        return $this;
    }
    
    /**
     * @param float $transactionFee
     *
     * @return Transaction
     */
    public function setTransactionFee(float $transactionFee): Transaction
    {
        $this->transactionFee = $transactionFee;
        
        return $this;
    }
    
    /**
     * @param string $transactionProvider
     *
     * @return Transaction
     */
    public function setTransactionProvider(string $transactionProvider): Transaction
    {
        $this->transactionProvider = $transactionProvider;
        
        return $this;
    }
    
    /**
     * @param float $transactionAmount
     *
     * @return Transaction
     */
    public function setTransactionAmount(float $transactionAmount): Transaction
    {
        $this->transactionAmount = $transactionAmount;
        
        return $this;
    }
    
    /**
     * @param int $transactionRecipientId
     *
     * @return Transaction
     */
    public function setTransactionRecipientId(int $transactionRecipientId): Transaction
    {
        $this->transactionRecipientId = $transactionRecipientId;
        
        return $this;
    }
    
    /**
     * @param string $transactionRecipientName
     *
     * @return Transaction
     */
    public function setTransactionRecipientName(string $transactionRecipientName): Transaction
    {
        $this->transactionRecipientName = $transactionRecipientName;
        
        return $this;
    }
    
    /**
     * @param string $transactionCurrency
     *
     * @return Transaction
     */
    public function setTransactionCurrency(string $transactionCurrency): Transaction
    {
        $this->transactionCurrency = $transactionCurrency;
        
        return $this;
    }
    
    /**
     * @param string $transactionDetails
     *
     * @return Transaction
     */
    public function setTransactionDetails(string $transactionDetails): Transaction
    {
        $this->transactionDetails = $transactionDetails;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTransactionDetails(): string
    {
        return $this->transactionDetails;
    }
    
    /**
     * @param int $entityId
     *
     * @return Transaction
     */
    public function setEntityId(int $entityId): EntityInterface
    {
        $this->entityId = $entityId;
        
        return $this;
    }
    
    /**
     * @param string $createdAt
     *
     * @return Transaction
     */
    public function setCreatedAt(string $createdAt): Transaction
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @param string $updatedAt
     *
     * @return Transaction
     */
    public function setUpdatedAt(string $updatedAt): Transaction
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}