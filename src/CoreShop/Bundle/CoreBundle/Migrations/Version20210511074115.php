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

use CoreShop\Component\Pimcore\Migration\SharedTranslation;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210511074115 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        SharedTranslation::add('coreshop.order_payment.total', 'de', 'Zahlung behinhaltet %items% Einträge für Betrag %total%.');
        SharedTranslation::add('coreshop.order_payment.total', 'de_CH', 'Zahlung behinhaltet %items% Einträge für Betrag %total%.');
        SharedTranslation::add('coreshop.order_payment.total', 'en', 'Payment contains %items% item(s) for a total of %total%.');
        SharedTranslation::add('coreshop.order_payment.total', 'it', 'Il pagamento contiene %items% voce/i per un totale di %total%.');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
