# Notification Rule Custom Type

In CoreShop, creating custom notification types is facilitated by dynamically registering them using tag attributes on
conditions and actions. This flexibility allows you to customize and extend the notification system to meet specific
requirements.

## Implementing a Custom Notification Type

To introduce a custom notification type, you can add a new condition or action and specify your unique type. This is
achieved by defining a service in your YAML configuration and assigning the appropriate tags.

### Example: Defining a Custom Notification Condition Service

Here's an example of defining a service for a custom notification condition:

```yaml
services:
    App\CoreShop\Notification\CustomNotificationCondition:
      tags:
        - { name: coreshop.notification_rule.condition, type: custom_condition, notification-type: custom, form-type: App\CoreShop\Form\Type\CoreShop\CustomConditionType }
```
