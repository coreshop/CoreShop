<?php

namespace CoreShop\Component\Rule\Model;

use Doctrine\Common\Collections\Collection;

interface RuleSubjectInterface
{
    /**
     * @return Collection|RuleInterface[]
     */
    public function getRules();

    /**
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     */
    public function removeRule(RuleInterface $rule);
}
