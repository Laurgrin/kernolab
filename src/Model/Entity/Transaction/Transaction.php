<?php

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\Entity\EntityInterface;

class Transaction implements EntityInterface
{
    /**
     * @var int
     */
    protected $entityId;
    
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
     * Returns the entity's ID (primary key).
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
    
    /**
     * Returns the time when entity was created.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Returns the time when the entity was last updated.
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }
    
    /**
     * @param string $transactionStatus
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
        
        return $this;
    }
    
    /**
     * @return float
     */
    public function getTransactionFee()
    {
        return $this->transactionFee;
    }
    
    /**
     * @return string
     */
    public function getTransactionProvider()
    {
        return $this->transactionProvider;
    }
    
    /**
     * @return float
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }
    
    /**
     * @return int
     */
    public function getTransactionRecipientId()
    {
        return $this->transactionRecipientId;
    }
    
    /**
     * @return string
     */
    public function getTransactionRecipientName()
    {
        return $this->transactionRecipientName;
    }
    
    /**
     * @return string
     */
    public function getTransactionCurrency()
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
}