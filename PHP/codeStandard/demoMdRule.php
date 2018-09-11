<?php
// phpcs:disable
include '/Users/fanghao/.composer/vendor/autoload.php';

class demoMdRule
       extends \PHPMD\AbstractRule
    implements \PHPMD\Rule\FunctionAware
{
    public function apply(\PHPMD\AbstractNode $node)
    {
        $this->addViolation($node);
    }
}