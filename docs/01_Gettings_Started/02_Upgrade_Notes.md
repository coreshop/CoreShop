# CoreShop Upgrade Notes

Always check this page for some important upgrade notes before updating to the latest coreshop build.

##Update from Version 1.1.2 (Build 106) to Version 1.2 (Build 116)

**Rules**   
TBD

**Order States**  
Order States has been completely removed from CoreShop 1.2. 
Instead, CoreShop is using the Pimcore Workflow to achieve flexible Order States / Statuses.

**Mail Rules**  
Normally, there is nothing special work to do. The CoreShop updater will install some valid Mail Rules for you.
Anyway, it's okay if you check the rules after the install - especially the action tab (check if all email has been placed correctly.).

**Email Template**   
All email templates obtaining a `object` parameter. Update your email templates like so:  

```html
    <!-- before -->
    <?= $this->order; ?>
    <!-- after -->
    <?= $this->object; ?>
```