<?php

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

final class NotificationRuleConditionPass extends AbstractNotificationRulePass
{
    protected function getType()
    {
        return 'conditions';
    }

    protected function getIdentifier()
    {
        return 'coreshop.notification_rule.conditions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.notification_rule.condition';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.notification_rule.conditions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.notification_rule.conditions';
    }
}
