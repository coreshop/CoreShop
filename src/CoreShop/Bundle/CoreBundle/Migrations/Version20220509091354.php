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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\Staticroute;

final class Version20220509091354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $existingRoute = Staticroute::getByName('coreshop_checkout_thank_you');

        if (null !== $existingRoute) {
            return;
        }

        $route = new Staticroute();
        $route->setId('coreshop_checkout_thank_you');
        $route->setName('coreshop_checkout_thank_you');
        $route->setPattern('/(\w+)\/shop\/checkout-thank-you\/(.*)$/');
        $route->setReverse('/%_locale/shop/checkout-thank-you/%token');
        $route->setController('CoreShop\Bundle\FrontendBundle\Controller\CheckoutController:thankYouAction');
        $route->setVariables('_locale,token');
        $route->setPriority(2);
        $route->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
