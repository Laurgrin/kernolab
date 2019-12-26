# The API
The API has 3 endpoints. All endpoints return JSON responses.

## Create transaction
|Method | Endpoint | Description |
|--|--|--|
| POST | /api/transaction/create | This request creates a new transaction. A single user can only create 10 transactions per hour, and has a lifetime transfer limit of 1000 in any currency (fee included). A fee of 10% is applied to the transaction, based from the amount transferred. If daily transactions (fee excluded) in any currency are over 100, the fee is reduced to 5% instead. Note that this endpoint only creates the transaction. Confirm API call is needed to confirm the transaction and prepare it for processing. Returns entity ID of the created transaction needed for further actions.

Request body:

| Argument | Description |
|--|--|
| `user_id` | The ID of the user creating the transaction |
| `transaction_details` | Custom message for the transaction |
| `transaction_recipient_id` | The account number/id of the recipient |
| `transaction_recipient_name` | The name of the recipient |
| `transaction_amount` | The amount to be transferred |
| `transaction_currency` | The currency in which the transaction is being made |

 * Response example on success: 
`{"status":"success","code":200,"message":"Transaction created successfully.","entity_id":1}`
* Response example on reaching hourly limit: `'{"status":"error","errors":[{"code":403,"message":"Hourly transaction limit exceeded."}]}'`
* Response example on reaching the lifetime limit: `{"status":"error","errors":[{"code":403,"message":"Maximum lifetime transactions reached."}]}`

## Confirm transaction
|Method | Endpoint | Description |
|--|--|--|
| POST | /api/transaction/confirm | This request takes the entity ID received from Create call and a 2FA verification code (111 for testing purposes) to confirm a transaction. 
Request body:

| Argument | Description |
|--|--|
| `entity_id` | The ID of the transaction being confirmed |
| `verification_code` | The 2FA code to confirm the transaction (111) |
* Response example on success: 
`{"status":"success","code":"200","message":"Transaction 1 confirmed successfully."}`
* Response example on wrong verification code: `'{"status":"error","errors":[{"code":401,"message":"Invalid verification code"}]}'`

## Get transaction
|Method | Endpoint | Description |
|--|--|--|
| GET | /api/transaction/get| This request takes the entity ID received from Create call to retrieve the transaction and return its data. 
Request body:

| Argument | Description |
|--|--|
| `entity_id` | The ID of the transaction being confirmed |
* Response example on success: 
`{"entity_id":1,"user_id":1,"transaction_status":"status",
"transaction_fee":1,"created_at":"123","updated_at":"456",
"transaction_provider":"provider","transaction_amount":10,
"transaction_recipient_id":2,
"transaction_recipient_name":"name",
"transaction_currency":"currency","transaction_details":"details"}`
* Response example on the transaction not existing: `'{"status":"error","errors":[{"code":404,"message":"Transaction with the id 2 not found"}]}'` 

## Transaction processing
Transaction processing doesn't have an external endpoint. Instead, it should be set in a crontab, running the `./src/Cron/processTransactions.php`. Depending on the needs, the `limit` argument can be set to limit the number of transactions processed per run and combined with the scheduling in the crontab itself.

## Extending
This task was written with being able to extend most features. Of course, there could many improvements, but I am not trying to write a framework here. It's not exactly an ORM for transaction handling, but I tried to abstract things to the best of my abilities. 
Things like QueryGenerator would need to be expanded if we we were to use more Criteria operands than just eq (=). Router might need to add extra handling if a new entity and it's controllers were added. 
Adding a new route should not be hard, as long as the existing conventions are followed. Routing file can be found in `./routing.json`
For transaction processor rules, only the ones in the task were added, and their parsing would need to be extended on the existing `TransactionProviderRule` class. Current rules can be found in `./src/Model/Entity/Transaction/providers.json`
## Missing things & testing
To be honest, I was learning unit testing as I did this task, as at my previous workplace unit (or any automated testing) was not part of our workflow. I could not figure out, in timely manner, how to test functions directly related to database operations. DBUnit seems to be no longer supported. Testing of endpoints was done manually using Postman. As for functional testing, I would need more time to learn how to use it, which would exceed the given time for this task.