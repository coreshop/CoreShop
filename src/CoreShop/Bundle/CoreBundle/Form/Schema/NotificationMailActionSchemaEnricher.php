<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Schema;

use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\OrderMailConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\StoreMailConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\StoreOrderMailConfigurationType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class NotificationMailActionSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array LOCALIZED_MAIL_TYPES = [
        OrderMailConfigurationType::class,
    ];

    private const array STORE_LOCALIZED_MAIL_TYPES = [
        StoreMailConfigurationType::class,
        StoreOrderMailConfigurationType::class,
    ];

    public function supports(string $formTypeClass): bool
    {
        return in_array($formTypeClass, self::LOCALIZED_MAIL_TYPES, true)
            || in_array($formTypeClass, self::STORE_LOCALIZED_MAIL_TYPES, true);
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $blockPrefix = in_array($formTypeClass, self::STORE_LOCALIZED_MAIL_TYPES, true)
            ? 'coreshop_store_localized_mail_documents'
            : 'coreshop_localized_mail_documents';

        foreach ($schema->fields as $field) {
            if ($field->name === 'mails') {
                $field->blockPrefixes = array_merge(
                    $field->blockPrefixes,
                    [$blockPrefix],
                );

                break;
            }
        }

        return $schema;
    }
}
