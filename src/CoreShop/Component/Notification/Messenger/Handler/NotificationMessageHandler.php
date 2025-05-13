<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Notification\Messenger\Handler;

use CoreShop\Component\Notification\Messenger\NotificationMessage;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Processor\RuleApplierInterface;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pimcore\Model\DataObject\Concrete;

class NotificationMessageHandler
{
    public function __construct(
        protected NotificationRuleRepositoryInterface $ruleRepository,
        protected RuleValidationProcessorInterface $ruleValidationProcessor,
        protected RuleApplierInterface $ruleApplier,
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function __invoke(NotificationMessage $message)
    {
        $type = $message->getType();
        $params = $message->getParams();

        if (is_subclass_of($message->getResourceType(), Concrete::class)) {
            $resource = Concrete::getById($message->getResourceId());
        } else {
            $objectManager = $this->managerRegistry->getManagerForClass($message->getResourceType());
            /** @psalm-var class-string $className */
            $className = $message->getResourceType();
            $resource = $objectManager?->find($className, $message->getResourceId());
        }

        if (!$resource instanceof ResourceInterface) {
            throw new \Exception('Resource could not be loaded');
        }

        $rules = $this->ruleRepository->findForType($type);

        //BC
        $params['subject'] = $resource;
        $params['resource'] = $resource;
        $params['object'] = $resource;

        /**
         * @var NotificationRuleInterface $rule
         */
        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($resource, $rule, ['params' => $params])) {
                $this->ruleApplier->applyRule($rule, $resource, $params);
            }
        }
    }
}
