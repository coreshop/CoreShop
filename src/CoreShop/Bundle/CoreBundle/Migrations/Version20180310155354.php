<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Db;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Document;

class Version20180310155354 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $db = Db::get();
        $sql = 'SELECT id FROM documents_email WHERE module = ? AND controller = ? AND (action = ? OR action = ?)';
        $results = $db->fetchAll($sql, ['CoreShopFrontendBundle', 'mail', 'mail', 'order-confirmation']);

        foreach ($results as $result) {
            /**
             * @var $email Document\Email
             */
            $email = Document\Email::getById($result['id']);

            if (!$email instanceof Document\Email) {
                continue;
            }

            $email->setController('@coreshop.frontend.controller.mail');
            $email->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
