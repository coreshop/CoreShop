<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Element\Note;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180213073553 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $oldNoteStates = [
            'Payment'            => 'payment',
            'Update Order'       => 'update_order',
            'Update Order Item'  => 'update_order_item',
            'Email'              => 'email',
            'OrderComment'       => 'order_comment',
            'Order State Change' => 'order_state_change'
        ];

        foreach($oldNoteStates as $currentKey => $newKey) {

            $noteListing = new Note\Listing();
            $noteListing->addConditionParam('type = ?', $currentKey);

            foreach($noteListing->load() as $note) {
                $note->setType($newKey);
                $note->save();
            }
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
