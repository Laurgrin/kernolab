<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\EntityNotFoundException;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Exception\RequestParameterException;

class Get extends AbstractTransactionController
{
    protected const REQUIRED_PARAMS = ['entity_id'];
    
    /**
     * Process a request and return a response
     *
     * @param array $requestParams
     *
     * @return JsonResponse
     * @throws \Kernolab\Exception\ApiException
     */
    public function execute(array $requestParams): JsonResponse
    {
        try {
            $this->requestValidator->validateRequest($requestParams, self::REQUIRED_PARAMS);
            $transaction = $this->transactionService->getTransactionByEntityId((int)$requestParams['entity_id']);
            
            $this->jsonResponse->addField('entity_id', $transaction->getEntityId())
                               ->addField('user_id', $transaction->getUserId())
                               ->addField('transaction_status', $transaction->getTransactionStatus())
                               ->addField('transaction_fee', $transaction->getTransactionFee())
                               ->addField('created_at', $transaction->getCreatedAt())
                               ->addField('updated_at', $transaction->getUpdatedAt())
                               ->addField('transaction_provider', $transaction->getTransactionProvider())
                               ->addField('transaction_amount', $transaction->getTransactionAmount())
                               ->addField('transaction_recipient_id', $transaction->getTransactionRecipientId())
                               ->addField('transaction_recipient_name', $transaction->getTransactionRecipientName())
                               ->addField('transaction_currency', $transaction->getTransactionCurrency())
                               ->addField('transaction_details', $transaction->getTransactionDetails());
        } catch (RequestParameterException $e) {
            $this->controllerExceptionHandler->handleRequestParameterException($e);
        } catch (MySqlPreparedStatementException $e) {
            $this->controllerExceptionHandler->handleMySqlPreparedStatementException($e);
        } catch (EntityNotFoundException $e) {
            $this->controllerExceptionHandler->handleEntityNotFoundException($e);
        } catch (MySqlConnectionException $e) {
            $this->controllerExceptionHandler->handleMySqlConnectionException($e);
        } catch (\TypeError $e) {
            $this->controllerExceptionHandler->handleTypeError($e);
        } catch (ConfigurationFileNotFoundException $e) {
            $this->controllerExceptionHandler->handleConfigurationFileNotFoundException($e);
        }
        
        return $this->jsonResponse;
    }
}