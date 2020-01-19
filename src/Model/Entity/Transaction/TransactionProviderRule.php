<?php declare(strict_types = 1);

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Exception\ConfigurationFileNotFoundException;

class TransactionProviderRule implements TransactionProviderRuleInterface
{
    /**
     * @var array
     */
    protected $providers;
    
    /**
     * Gets the provider file.
     *
     * @return array
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     * @throws \JsonException
     */
    protected function getProviders(): array
    {
        if ($this->providers === null) {
            if (!is_readable(PROVIDER_PATH)) {
                throw new ConfigurationFileNotFoundException(
                    'Provider configuration file not found or is not readable.'
                );
            }
            
            $this->providers = json_decode(
                file_get_contents(PROVIDER_PATH),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        
        return $this->providers;
    }
    
    /**
     * Apply a transaction provider rule to transaction assoc. array.
     *
     * @param array $params
     *
     * @return array
     * @throws \JsonException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     * @throws \Exception
     */
    public function applyProviderRules(array $params): array
    {
        $providers = $this->getProviders();
        
        $currency = $params['transaction_currency'];
        if (array_key_exists($currency, $providers)) {
            return $this->applyRuleset($providers[$currency], $params);
        }
        
        return $this->applyRuleset($providers['Default'], $params);
    }
    
    /**
     * Applies the specified ruleset to the entity params.
     *
     * @param array $ruleSet The rule set for the provider rules to be applied
     * @param array $params  an associative array of the entity data
     *
     * @return array
     * @throws \Exception
     */
    protected function applyRuleset(array $ruleSet, array $params): array
    {
        $params['transaction_provider'] = $ruleSet['name'];
        foreach ($ruleSet['rules'] as $rule) {
            switch ($rule['rule']) {
                case 'length':
                    $params[$rule['field']] = substr($params[$rule['field']], 0, $rule['value']);
                    break;
                case 'random_int':
                    $params[$rule['field']] .= random_int(0, $rule['value']);
                    break;
            }
        }
        
        return $params;
    }
}