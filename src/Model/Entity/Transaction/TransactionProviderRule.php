<?php  declare(strict_types = 1);

namespace Kernolab\Model\Entity\Transaction;

class TransactionProviderRule implements TransactionProviderRuleInterface
{
    /**
     * @var array
     */
    protected $providers;
    
    /**
     * TransactionProviderRule constructor.
     */
    public function __construct()
    {
        $this->providers = json_decode(file_get_contents(__DIR__ . "/providers.json"), true);
    }
    
    /**
     * Apply a transaction provider rule to transaction assoc. array.
     *
     * @param array $params
     *
     * @return array
     */
    public function applyProviderRules(array $params): array
    {
        $currency = $params["transaction_currency"];
        if (array_key_exists($currency, $this->providers)) {
            return $this->applyRuleset($this->providers[$currency], $params);
        } else {
            return $this->applyRuleset($this->providers["Default"], $params);
        }
    }
    
    /**
     * Applies the specified ruleset to the entity params.
     *
     * @param array $ruleSet The rule set for the provider rules to be applied
     * @param array $params  an associative array of the entity data
     *
     * @return array
     */
    protected function applyRuleset(array $ruleSet, array $params): array
    {
        $params["transaction_provider"] = $ruleSet["name"];
        foreach ($ruleSet["rules"] as $rule) {
            switch ($rule["rule"]) {
                case "length":
                    $params[$rule["field"]] = substr($params[$rule["field"]], 0, $rule["value"]);
                    break;
                case "random_int":
                    $params[$rule["field"]] .= rand(0, $rule["value"]);
                    break;
            }
        }
        
        return $params;
    }
}