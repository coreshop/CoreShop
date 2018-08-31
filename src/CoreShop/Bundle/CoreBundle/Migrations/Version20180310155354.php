<?php

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
