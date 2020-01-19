<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\DateTimeException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Exception\RequestParameterException;
use Kernolab\Exception\TransactionCreationException;
use Kernolab\Service\Logger;

class Create extends AbstractTransactionController
{
    protected const REQUIRED_KEYS = [
        'user_id',
        'transaction_details',
        'transaction_recipient_id',
        'transaction_recipient_name',
        'transaction_amount',
        'transaction_currency',
    ];
    
    /**
     * Process a request and return a response
     *
     * @param array $requestParams
     *
     * @return \Kernolab\Controller\JsonResponse
     * @throws \Kernolab\Exception\ApiException
     */
    public function execute(array $requestParams): JsonResponse
    {
        try {
            $this->requestValidator->validateRequest($requestParams, self::REQUIRED_KEYS);
            
            $userId      = (int)$requestParams['user_id'];
            $transaction = $this->transactionService->checkUserTransactionCount($userId)
                                                    ->checkUserLifetimeTransactionAmount($userId)
                                                    ->setTransactionFee($userId, $requestParams)
                                                    ->createTransaction($requestParams);
            
            $this->jsonResponse->addField('status', 'success')
                               ->addField('code', 200)
                               ->addField('message', 'Transaction created successfully.')
                               ->addField('entity_id', $transaction->getEntityId());
        } catch (RequestParameterException $e) {
            $this->controllerExceptionHandler->handleRequestParameterException($e);
        } catch (DateTimeException $e) {
            $this->controllerExceptionHandler->handleDateTimeException($e);
        } catch (HourlyTransactionException $e) {
            $this->controllerExceptionHandler->handleHourlyTransactionException($e);
        } catch (LifetimeTransactionAmountException $e) {
            $this->controllerExceptionHandler->handleLifetimeTransactionAmountException($e);
        } catch (TransactionCreationException $e) {
            $this->controllerExceptionHandler->handleTransactionCreationException($e);
        } catch (MySqlPreparedStatementException $e) {
            $this->controllerExceptionHandler->handleMySqlPreparedStatementException($e);
        } catch (\TypeError $e) {
            $this->controllerExceptionHandler->handleTypeError($e);
        } catch (MySqlConnectionException $e) {
            $this->controllerExceptionHandler->handleMySqlConnectionException($e);
        } catch (ConfigurationFileNotFoundException $e) {
            $this->controllerExceptionHandler->handleConfigurationFileNotFoundException($e);
        }
        
        return $this->jsonResponse;
    }
}