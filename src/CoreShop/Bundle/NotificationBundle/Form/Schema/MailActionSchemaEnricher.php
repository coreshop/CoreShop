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

namespace CoreShop\Bundle\NotificationBundle\Form\Schema;

use CoreShop\Bundle\NotificationBundle\Form\Type\Rule\Action\MailActionConfigurationType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class MailActionSchemaEnricher implements FormSchemaEnricherInterface
{
    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === MailActionConfigurationType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        foreach ($schema->fields as $field) {
            if ($field->name === 'mails') {
                $field->blockPrefixes = array_merge(
                    $field->blockPrefixes,
                    ['coreshop_localized_mail_documents'],
                );

                break;
            }
        }

        return $schema;
    }
}
