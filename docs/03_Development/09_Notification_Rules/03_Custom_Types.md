# CoreShop Notification Rule Custom Type

Notification Types are registered dynamically by using tag-attributes on conditions and actions. If you want to have your own
type, you can do so by adding a new condition or action and specify your own type:

```yaml
services:
  app.coreshop.notification_rule.condition.order.custom_notification_condition:
    class: AppBundle\CoreShop\Notification\CustomNotificationCondition
    tags:
      - { name: coreshop.notification_rule.condition, type: custom_condition, notification-type: custom, form-type: AppBundle\Form\Type\CoreShop\CustomConditionType }

```
