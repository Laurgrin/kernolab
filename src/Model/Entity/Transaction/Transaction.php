<?php

namespace Kernolab\Model;

class Transaction implements EntityInterface
{
    /**
     * @var int
     */
    protected $transactionId;
    
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
     * Transaction constructor.
     *
     * @param $transactionId
     * @param $userId
     * @param $transactionStatus
     * @param $transactionFee
     * @param $createdAt
     * @param $updatedAt
     * @param $transactionProvider
     * @param $transactionAmount
     * @param $transactionRecipientId
     * @param $transactionRecipientName
     * @param $transactionCurrency
     */
    public function __construct(
        $transactionId,
        $userId,
        $transactionStatus,
        $transactionFee,
        $createdAt,
        $updatedAt,
        $transactionProvider,
        $transactionAmount,
        $transactionRecipientId,
        $transactionRecipientName,
        $transactionCurrency
    ) {
        $this->transactionId            = $transactionId;
        $this->userId                   = $userId;
        $this->transactionStatus        = $transactionStatus;
        $this->transactionFee           = $transactionFee;
        $this->createdAt                = $createdAt;
        $this->updatedAt                = $updatedAt;
        $this->transactionProvider      = $transactionProvider;
        $this->transactionAmount        = $transactionAmount;
        $this->transactionRecipientId   = $transactionRecipientId;
        $this->transactionRecipientName = $transactionRecipientName;
        $this->transactionCurrency      = $transactionCurrency;
    }
    
    /**
     * Returns the entity's ID (primary key).
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->transactionId;
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
     * @return \Kernolab\Model\Transaction
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
     * @param string $updatedAt
     *
     * @return Transaction
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
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
}