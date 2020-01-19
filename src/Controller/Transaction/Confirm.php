<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\EntityNotFoundException;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Exception\RequestParameterException;
use Kernolab\Exception\TransactionConfirmationException;

class Confirm extends AbstractTransactionController
{
    protected const REQUIRED_PARAMS = ['entity_id', 'verification_code'];
    
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
            $entityId         = (int)$requestParams['entity_id'];
            $verificationCode = (int)$requestParams['verification_code'];
            $transaction      = $this->transactionService->confirmTransaction($entityId, $verificationCode);
            
            $this->jsonResponse->addField('status', 'success')
                               ->addField('code', '200')
                               ->addField('message', sprintf('Transaction %s confirmed successfully.',
                                                             $transaction->getEntityId()
                                                   )
                               );
        } catch (RequestParameterException $e) {
            $this->controllerExceptionHandler->handleRequestParameterException($e);
        } catch (MySqlPreparedStatementException $e) {
            $this->controllerExceptionHandler->handleMySqlPreparedStatementException($e);
        } catch (TransactionConfirmationException $e) {
            $this->controllerExceptionHandler->handleTransactionConfirmationException($e);
        } catch (\TypeError $e) {
            $this->controllerExceptionHandler->handleTypeError($e);
        } catch (EntityNotFoundException $e) {
            $this->controllerExceptionHandler->handleEntityNotFoundException($e);
        } catch (MySqlConnectionException $e) {
            $this->controllerExceptionHandler->handleMySqlConnectionException($e);
        } catch (ConfigurationFileNotFoundException $e) {
            $this->controllerExceptionHandler->handleConfigurationFileNotFoundException($e);
        }
        
        return $this->jsonResponse;
    }
}