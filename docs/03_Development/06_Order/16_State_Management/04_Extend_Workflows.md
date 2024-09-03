# State Machine - Extend Workflows

It's possible to extend all available CoreShop Workflow.

## Example A: Extend Shipment Workflow

### Workflow Configuration

```yml
core_shop_workflow:
    state_machine:
        coreshop_shipment:
            # define a new place "reviewed"
            places:
                - reviewed
            # define a new transition "review"
            transitions:
                review:
                    from: [new, ready]
                    to: reviewed
            # add some colors for better ux
            place_colors:
                reviewed: '#2f819e'
            transition_colors:
                review: '#2f819e'
```

### Add translations

Just use the Pimcore Backend/Frontend translation or just add it via default symfony translation context:

```yml
# app/Resources/translations/admin.en.yml
coreshop_workflow_transition_coreshop_shipment_review: 'Review'
coreshop_workflow_state_coreshop_shipment_reviewed: 'Reviewed'

# app/Resources/translations/messages.en.yml
coreshop.ui.workflow.state.coreshop_shipment.reviewed: 'Shipment under Review'
```

### Inform CoreShop about new Transition of Shipment Workflow

Not all transitions should be available in backend.
To allow the transition to show up, you need to implement a simple event listener:

```yml
# app/config/services
App\CoreShop\EventListener\WorkflowListener:
    autowire: true
    tags:
        - { name: kernel.event_listener, event: coreshop.workflow.valid_transitions, method: parseTransitions}
```

```php
<?php

namespace App\CoreShop\EventListener;

use CoreShop\Bundle\OrderBundle\Event\WorkflowTransitionEvent;

class WorkflowListener
{
    public function parseTransitions(WorkflowTransitionEvent $event)
    {
        $workflowName = $event->getWorkflowName();
        if($workflowName === 'coreshop_shipment') {
            $event->addAllowedTransitions(['review']);
        }
    }
}
```

## Example B: Change default Transition Behaviour of Shipment Workflow

> **Note:** Be careful while changing transitions. Test your application if a workflow still runs smoothly after
> changing it!

In this example we want to change the default shipping behavior.

### Workflow before:

`ready` -> `shipped` -> `cancelled`

### Workflow after:

`ready` -> `reviewed` -> `shipped` -> `cancelled`

### Workflow Configuration

```yml
core_shop_workflow:
    state_machine:
        coreshop_shipment:
            # define a new place "reviewed"
            places:
                - reviewed
            # define a new transition "review"
            transitions:
                review:
                    from: [ready]
                    to: reviewed
                # override the default "ship" transition
                # which only allows [ready] as valid "from" dispatcher
                ship:
                    from: [reviewed]
                    to: shipped
            # add some colors for better ux
            place_colors:
                reviewed: '#2f819e'
            transition_colors:
                review: '#2f819e'
```

### Example C: Callback Listener

If you need to implement some further business logic after the `coreshop_shipment` state has changed to `reviewed`
you need to define a callback:

> **Note:** Please make sure your service is public available!

```yml
core_shop_workflow:
    state_machine:
        coreshop_shipment:
            callbacks:
                after:
                    do_something_after_review:
                        on: ['review']
                        do: ['@your_service', 'yourAction']
                        # in this context, "object" is the shipment item
                        args: ['object']
                        priority: -10 # fire action early!
```
