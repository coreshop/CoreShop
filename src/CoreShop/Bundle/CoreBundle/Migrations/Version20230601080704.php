<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601080704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE coreshop_payment_provider_rule_group (id INT AUTO_INCREMENT NOT NULL, priority INT NOT NULL, stopPropagation TINYINT(1) NOT NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, paymentProviderId INT NOT NULL, paymentProviderRuleId INT DEFAULT NULL, INDEX IDX_B47BF83C7FADB943 (paymentProviderId), INDEX IDX_B47BF83CD9601F4E (paymentProviderRuleId), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
            CREATE TABLE coreshop_payment_provider_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
            CREATE TABLE coreshop_payment_provider_rule_conditions (payment_provider_rule_id INT NOT NULL, condition_id INT NOT NULL, INDEX IDX_A6F787FB6D070DBB (payment_provider_rule_id), INDEX IDX_A6F787FB887793B6 (condition_id), PRIMARY KEY(payment_provider_rule_id, condition_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
            CREATE TABLE coreshop_payment_provider_rule_actions (payment_provider_rule_id INT NOT NULL, action_id INT NOT NULL, INDEX IDX_DED589CD6D070DBB (payment_provider_rule_id), INDEX IDX_DED589CD9D32F035 (action_id), PRIMARY KEY(payment_provider_rule_id, action_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
            ALTER TABLE coreshop_payment_provider_rule_group ADD CONSTRAINT FK_B47BF83C7FADB943 FOREIGN KEY (paymentProviderId) REFERENCES coreshop_payment_provider (id);
            ALTER TABLE coreshop_payment_provider_rule_group ADD CONSTRAINT FK_B47BF83CD9601F4E FOREIGN KEY (paymentProviderRuleId) REFERENCES coreshop_payment_provider_rule (id);
            ALTER TABLE coreshop_payment_provider_rule_conditions ADD CONSTRAINT FK_A6F787FB6D070DBB FOREIGN KEY (payment_provider_rule_id) REFERENCES coreshop_payment_provider_rule (id) ON DELETE CASCADE;
            ALTER TABLE coreshop_payment_provider_rule_conditions ADD CONSTRAINT FK_A6F787FB887793B6 FOREIGN KEY (condition_id) REFERENCES coreshop_rule_condition (id) ON DELETE CASCADE;
            ALTER TABLE coreshop_payment_provider_rule_actions ADD CONSTRAINT FK_DED589CD6D070DBB FOREIGN KEY (payment_provider_rule_id) REFERENCES coreshop_payment_provider_rule (id) ON DELETE CASCADE;
            ALTER TABLE coreshop_payment_provider_rule_actions ADD CONSTRAINT FK_DED589CD9D32F035 FOREIGN KEY (action_id) REFERENCES coreshop_rule_action (id) ON DELETE CASCADE;
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
