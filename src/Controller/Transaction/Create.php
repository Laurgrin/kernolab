<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\DateTimeException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
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
            foreach ($e->getMissingKeys() as $missingKey) {
                $this->jsonResponse->addError(400, sprintf('Missing required key %s', $missingKey));
            }
        } catch (DateTimeException $e) {
            $this->jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } catch (HourlyTransactionException $e) {
            $this->jsonResponse->addError(403, $e->getMessage());
        } catch (LifetimeTransactionAmountException $e) {
            $this->jsonResponse->addError(403, $e->getMessage());
        } catch (TransactionCreationException $e) {
            $this->jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } catch (MySqlPreparedStatementException $e) {
            $this->jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } catch (\TypeError $e) {
            $this->jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } finally {
            return $this->jsonResponse;
        }
    }
}