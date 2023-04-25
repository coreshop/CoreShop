# CoreShop Notification Bundle

CoreShop Notification Bundle handles all communication between CoreShop and the outside world. It provides a basic set of data for Notifications like:
 - Notification Rules
 - Notification Rule Conditions
 - Notification Rule Actions

> This Bundle can be used separately, but doesn't provide any detail information how to use it.
 
## Usage
Notifications run async in a Symfony messenger queue:

```
bin/console messenger:consume coreshop_notification coreshop_index --time-limit=300
```
