<?php

/* Bootstrapping */
$rootPath = realpath(__DIR__);
define("ROOT_PATH", $rootPath);
define("ENV_PATH", ROOT_PATH . "/.env");

require_once(__DIR__ . "/vendor/autoload.php");

$transaction = new \Kernolab\Model\Entity\Transaction\Transaction();
$transaction->setTransactionId(0)
            ->setUserId(1)
            ->setTransactionAmount("22.25")
            ->setTransactionFee("2.05")
            ->setTransactionCurrency("EUR")
            ->setTransactionProvider("Test")
            ->setTransactionRecipientId(2)
            ->setTransactionRecipientName("Name Surname")
            ->setTransactionStatus("processing");

$transaction2 = new Kernolab\Model\Entity\Transaction\Transaction();
$transaction2->setTransactionId(12)
            ->setUserId(1)
            ->setTransactionAmount("22.25")
            ->setTransactionFee("2.05")
            ->setTransactionCurrency("EUR")
            ->setTransactionProvider("Test")
            ->setTransactionRecipientId(2)
            ->setTransactionRecipientName("Name Surname")
            ->setTransactionStatus("success");

try {
    $dataSource =
        new \Kernolab\Model\DataSource\MySql\DataSource(new \Kernolab\Model\DataSource\MySql\CriteriaParser(), "transaction");
    echo "Rows affected: " . $dataSource->set([$transaction, $transaction2]) . PHP_EOL;
} catch (\Kernolab\Exception\MySqlConnectionException $e) {
    die($e->getMessage());
} catch (\Kernolab\Exception\MySqlPreparedStatementException $e) {
    die($e->getMessage());
}

