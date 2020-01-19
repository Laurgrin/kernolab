<?php declare(strict_types = 1);
namespace Test\Unit\Model\Entity\Transaction;

use Kernolab\Model\Entity\Transaction\TransactionProviderRule;
use PHPUnit\Framework\TestCase;

class TransactionProviderRuleTest extends TestCase
{
    private $providers;
    
    private $transactionProvider;
    
    protected function setUp(): void
    {
        $this->providers           = [
            'EUR'     => [
                'name'  => 'Megacash',
                'rules' => [
                    'field' => 'transaction_details',
                    'rule'  => 'length',
                    'value' => '20',
                ],
            ],
            'Default' => [
                'name'  => 'Megacash',
                'rules' => [
                    'field' => 'transaction_details',
                    'rule'  => 'random_int',
                    'value' => '50',
                ],
            ],
        ];
        $this->transactionProvider = new TransactionProviderRule();
    }
    
    protected function tearDown(): void
    {
        $this->transactionProvider = null;
        $this->providers           = null;
    }
    
    /**
     * @dataProvider applyProviderRulesMegacashProvider
     * @runInSeparateProcess
     *
     * @param $params
     * @param $expected
     */
    public function testApplyProviderRulesMegacash($params, $expected): void
    {
        $actual = $this->transactionProvider->applyProviderRules($params);
        $this->assertEquals($expected, $actual);
    }
    
    public function applyProviderRulesMegacashProvider(): array
    {
        return [
            [
                [
                    'transaction_details'  => '20 characters here: this should not be seen',
                    'transaction_currency' => 'EUR',
                ],
                [
                    'transaction_details'  => '20 characters here: ',
                    'transaction_currency' => 'EUR',
                    'transaction_provider' => 'Megacash',
                ],
            ],
        ];
    }
    
    /**
     * @dataProvider applyProviderRulesSupermoneyProvider
     * @runInSeparateProcess
     *
     * @param $params
     * @param $expected
     */
    public function testApplyProviderRulesSupermoney($params, $expected): void
    {
        $actual = $this->transactionProvider->applyProviderRules($params);
        $this->assertRegExp("/Details \d+/", $actual['transaction_details']);
        $this->assertEquals($expected['transaction_provider'], $actual['transaction_provider']);
    }
    
    public function applyProviderRulesSupermoneyProvider(): array
    {
        return [
            [
                [
                    'transaction_details'  => 'Details ',
                    'transaction_currency' => 'USD',
                ],
                [
                    'transaction_details'  => 'Details ',
                    'transaction_currency' => 'USD',
                    'transaction_provider' => 'Supermoney',
                ],
            ],
        ];
    }
}
