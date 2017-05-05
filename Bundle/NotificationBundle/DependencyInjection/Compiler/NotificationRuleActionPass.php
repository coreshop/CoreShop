<?php

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

final class NotificationRuleActionPass extends AbstractNotificationRulePass
{
    protected function getType()
    {
        return 'actions';
    }

    protected function getIdentifier()
    {
        return 'coreshop.notification_rule.actions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.notification_rule.action';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.notification_rule.actions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.notification_rule.actions';
    }
}
