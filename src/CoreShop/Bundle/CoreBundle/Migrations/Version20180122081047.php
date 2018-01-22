<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\GridConfig;
use Pimcore\Model\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180122081047 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws \Exception
     */
    public function up(Schema $schema)
    {
        \Pimcore::collectGarbage();
        \Pimcore\Cache::clearAll();

        $this->createGridConfig($this->getGridConfig('order', 'en'), 'Order Overview', 'order');
        $this->createGridConfig($this->getGridConfig('order', 'de'), 'Bestellübersicht', 'order');

        $this->createGridConfig($this->getGridConfig('quote', 'en'), 'Quote Overview', 'quote');
        $this->createGridConfig($this->getGridConfig('quote', 'de'), 'Angebotsübersicht', 'quote');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    /**
     * @param $data
     * @param $name
     * @param $className
     * @throws \Exception
     */
    protected function createGridConfig($data, $name, $className)
    {
        $list = new GridConfig\Listing();
        $list->addConditionParam('name = ?', $name);
        $elements = $list->load();

        if (count($elements) === 0) {

            $classId = $this->container->getParameter('coreshop.model.' . $className . '.pimcore_class_id');

            $data['classId'] = $classId;

            $gridConfig = new GridConfig();
            $configDataEncoded = json_encode($data);
            $gridConfig->setName($name);
            $gridConfig->setShareGlobally(true);
            $gridConfig->setConfig($configDataEncoded);
            $gridConfig->setOwnerId($this->getAdminUser()->getId());
            $gridConfig->setSearchType('folder');
            $gridConfig->setClassId($classId);
            $gridConfig->save();

        }
    }

    /**
     * Get user from user proxy object which is registered on security component
     *
     * @param bool $proxyUser Return the proxy user (UserInterface) instead of the pimcore model
     *
     * @return UserProxy|User
     */
    protected function getAdminUser($proxyUser = false)
    {
        $resolver = $this->container->get(TokenStorageUserResolver::class);

        if ($proxyUser) {
            return $resolver->getUserProxy();
        } else {
            return $resolver->getUser();
        }
    }

    /**
     * @param $type
     * @param $language
     * @return mixed
     */
    protected function getGridConfig($type, $language)
    {
        $data = [
            'order' => [
                'de' => [
                    'language'           => 'de',
                    'sortinfo'           =>
                        [
                            'field'     => 'id',
                            'direction' => 'DESC',
                        ],
                    'classId'            => null,
                    'columns'            =>
                        [
                            'id'             =>
                                [
                                    'name'        => 'id',
                                    'position'    => 1,
                                    'hidden'      => false,
                                    'width'       => 80,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'id',
                                            'label'  => 'ID',
                                            'type'   => 'system',
                                            'layout' =>
                                                [
                                                    'title'     => 'id',
                                                    'name'      => 'id',
                                                    'datatype'  => 'data',
                                                    'fieldtype' => 'system',
                                                ],
                                        ],
                                ],
                            '#5a65a6bced7f5' =>
                                [
                                    'name'        => '#5a65a6bced7f5',
                                    'position'    => 2,
                                    'hidden'      => false,
                                    'width'       => 109,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Bestellnummer',
                                                    'type'   => 'operator',
                                                    'class'  => 'Trimmer',
                                                    'trim'   => 0,
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Bestellnummer (orderNumber)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'orderNumber',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced7f5',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced85d' =>
                                [
                                    'name'        => '#5a65a6bced85d',
                                    'position'    => 3,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'            => 'Email',
                                                    'type'             => 'operator',
                                                    'class'            => 'AnyGetter',
                                                    'attribute'        => 'email',
                                                    'param1'           => '',
                                                    'isArrayType'      => false,
                                                    'forwardAttribute' => '',
                                                    'forwardParam1'    => '',
                                                    'returnLastResult' => false,
                                                    'childs'           =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Kunde (customer)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'Href',
                                                                    'attribute' => 'customer',
                                                                    'dataType'  => 'href',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced85d',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced8d0' =>
                                [
                                    'name'        => '#5a65a6bced8d0',
                                    'position'    => 4,
                                    'hidden'      => false,
                                    'width'       => 180,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Name',
                                                    'type'   => 'operator',
                                                    'class'  => 'Concatenator',
                                                    'glue'   => ' ',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'            => 'First Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'firstname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Rechnungsadresse (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            1 =>
                                                                [
                                                                    'label'            => 'Last Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'lastname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Rechnungsadresse (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced8d0',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced959' =>
                                [
                                    'name'        => '#5a65a6bced959',
                                    'position'    => 5,
                                    'hidden'      => false,
                                    'width'       => 190,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Bestellstatus',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'orderState'     => 'orderState',
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Order State (orderState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'orderState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                    'highlightLabel' => true,
                                                ],
                                            'key'        => '#5a65a6bced959',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced9e2' =>
                                [
                                    'name'        => '#5a65a6bced9e2',
                                    'position'    => 6,
                                    'hidden'      => false,
                                    'width'       => 231,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Zahlungsstatus',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'highlightLabel' => true,
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Payment State (paymentState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'paymentState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced9e2',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bceda82' =>
                                [
                                    'name'        => '#5a65a6bceda82',
                                    'position'    => 7,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Lieferstatus',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'highlightLabel' => true,
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Shipping State (shippingState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'shippingState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bceda82',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bcedae7' =>
                                [
                                    'name'        => '#5a65a6bcedae7',
                                    'position'    => 8,
                                    'hidden'      => false,
                                    'width'       => 145,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Gesamtpreis',
                                                    'type'   => 'operator',
                                                    'class'  => 'PriceFormatter',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Total Brutto (totalGross)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'totalGross',
                                                                    'dataType'  => 'coreShopMoney',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bcedae7',
                                        ],
                                    'isOperator'  => true,
                                ],
                            'orderDate'      =>
                                [
                                    'name'        => 'orderDate',
                                    'position'    => 9,
                                    'hidden'      => false,
                                    'width'       => 183,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'orderDate',
                                            'label'  => 'Order Date',
                                            'type'   => 'datetime',
                                            'layout' =>
                                                [
                                                    'fieldtype'       => 'datetime',
                                                    'queryColumnType' => 'bigint(20)',
                                                    'columnType'      => 'bigint(20)',
                                                    'phpdocType'      => '\\Carbon\\Carbon',
                                                    'defaultValue'    => null,
                                                    'useCurrentDate'  => false,
                                                    'name'            => 'orderDate',
                                                    'title'           => 'Order Date',
                                                    'tooltip'         => '',
                                                    'mandatory'       => false,
                                                    'noteditable'     => true,
                                                    'index'           => false,
                                                    'locked'          => false,
                                                    'style'           => '',
                                                    'permissions'     => null,
                                                    'datatype'        => 'data',
                                                    'relationType'    => false,
                                                    'invisible'       => false,
                                                    'visibleGridView' => false,
                                                    'visibleSearch'   => false,
                                                ],
                                            'width'  => 224,
                                        ],
                                ],
                        ],
                    'onlyDirectChildren' => false,
                    'pageSize'           => 25,
                    'pimcore_version'    => '5.1.0',
                    'pimcore_revision'   => 166,
                ],
                'en' => [
                    'language'           => 'en',
                    'sortinfo'           =>
                        [
                            'field'     => 'id',
                            'direction' => 'DESC',
                        ],
                    'classId'            => null,
                    'columns'            =>
                        [
                            'id'             =>
                                [
                                    'name'        => 'id',
                                    'position'    => 1,
                                    'hidden'      => false,
                                    'width'       => 80,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'id',
                                            'label'  => 'ID',
                                            'type'   => 'system',
                                            'layout' =>
                                                [
                                                    'title'     => 'id',
                                                    'name'      => 'id',
                                                    'datatype'  => 'data',
                                                    'fieldtype' => 'system',
                                                ],
                                        ],
                                ],
                            '#5a65a6bced7f5' =>
                                [
                                    'name'        => '#5a65a6bced7f5',
                                    'position'    => 2,
                                    'hidden'      => false,
                                    'width'       => 109,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Order Number',
                                                    'type'   => 'operator',
                                                    'class'  => 'Trimmer',
                                                    'trim'   => 0,
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Order Number (orderNumber)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'orderNumber',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced7f5',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced85d' =>
                                [
                                    'name'        => '#5a65a6bced85d',
                                    'position'    => 3,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'            => 'Email',
                                                    'type'             => 'operator',
                                                    'class'            => 'AnyGetter',
                                                    'attribute'        => 'email',
                                                    'param1'           => '',
                                                    'isArrayType'      => false,
                                                    'forwardAttribute' => '',
                                                    'forwardParam1'    => '',
                                                    'returnLastResult' => false,
                                                    'childs'           =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Customer (customer)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'Href',
                                                                    'attribute' => 'customer',
                                                                    'dataType'  => 'href',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced85d',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced8d0' =>
                                [
                                    'name'        => '#5a65a6bced8d0',
                                    'position'    => 4,
                                    'hidden'      => false,
                                    'width'       => 180,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Name',
                                                    'type'   => 'operator',
                                                    'class'  => 'Concatenator',
                                                    'glue'   => ' ',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'            => 'First Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'firstname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Invoice Address (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            1 =>
                                                                [
                                                                    'label'            => 'Last Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'lastname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Invoice Address (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced8d0',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced959' =>
                                [
                                    'name'        => '#5a65a6bced959',
                                    'position'    => 5,
                                    'hidden'      => false,
                                    'width'       => 190,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Order State',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'orderState'     => 'orderState',
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Order State (orderState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'orderState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                    'highlightLabel' => true,
                                                ],
                                            'key'        => '#5a65a6bced959',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced9e2' =>
                                [
                                    'name'        => '#5a65a6bced9e2',
                                    'position'    => 6,
                                    'hidden'      => false,
                                    'width'       => 231,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Payment State',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'highlightLabel' => true,
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Payment State (paymentState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'paymentState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced9e2',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bceda82' =>
                                [
                                    'name'        => '#5a65a6bceda82',
                                    'position'    => 7,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'          => 'Shipping State',
                                                    'type'           => 'operator',
                                                    'class'          => 'OrderState',
                                                    'highlightLabel' => true,
                                                    'childs'         =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Shipping State (shippingState)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'shippingState',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bceda82',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bcedae7' =>
                                [
                                    'name'        => '#5a65a6bcedae7',
                                    'position'    => 8,
                                    'hidden'      => false,
                                    'width'       => 145,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Total Amount',
                                                    'type'   => 'operator',
                                                    'class'  => 'PriceFormatter',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Total gross (totalGross)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'totalGross',
                                                                    'dataType'  => 'coreShopMoney',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bcedae7',
                                        ],
                                    'isOperator'  => true,
                                ],
                            'orderDate'      =>
                                [
                                    'name'        => 'orderDate',
                                    'position'    => 9,
                                    'hidden'      => false,
                                    'width'       => 183,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'orderDate',
                                            'label'  => 'Order Date',
                                            'type'   => 'datetime',
                                            'layout' =>
                                                [
                                                    'fieldtype'       => 'datetime',
                                                    'queryColumnType' => 'bigint(20)',
                                                    'columnType'      => 'bigint(20)',
                                                    'phpdocType'      => '\\Carbon\\Carbon',
                                                    'defaultValue'    => null,
                                                    'useCurrentDate'  => false,
                                                    'name'            => 'orderDate',
                                                    'title'           => 'Order Date',
                                                    'tooltip'         => '',
                                                    'mandatory'       => false,
                                                    'noteditable'     => true,
                                                    'index'           => false,
                                                    'locked'          => false,
                                                    'style'           => '',
                                                    'permissions'     => null,
                                                    'datatype'        => 'data',
                                                    'relationType'    => false,
                                                    'invisible'       => false,
                                                    'visibleGridView' => false,
                                                    'visibleSearch'   => false,
                                                ],
                                            'width'  => 224,
                                        ],
                                ],
                        ],
                    'onlyDirectChildren' => false,
                    'pageSize'           => 25,
                    'pimcore_version'    => '5.1.0',
                    'pimcore_revision'   => 166,
                ]
            ],
            'quote' => [
                'de' => [
                    'language'           => 'de',
                    'sortinfo'           =>
                        [
                            'field'     => 'id',
                            'direction' => 'DESC',
                        ],
                    'classId'            => null,
                    'columns'            =>
                        [
                            'id'             =>
                                [
                                    'name'        => 'id',
                                    'position'    => 1,
                                    'hidden'      => false,
                                    'width'       => 80,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'id',
                                            'label'  => 'ID',
                                            'type'   => 'system',
                                            'layout' =>
                                                [
                                                    'title'     => 'id',
                                                    'name'      => 'id',
                                                    'datatype'  => 'data',
                                                    'fieldtype' => 'system',
                                                ],
                                        ],
                                ],
                            '#5a65a6bced7f5' =>
                                [
                                    'name'        => '#5a65a6bced7f5',
                                    'position'    => 2,
                                    'hidden'      => false,
                                    'width'       => 109,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Angebotsnummer',
                                                    'type'   => 'operator',
                                                    'class'  => 'Trimmer',
                                                    'trim'   => 0,
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Angebotsnummer (quoteNumber)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'quoteNumber',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                ],
                            '#5a65a6bced85d' =>
                                [
                                    'name'        => '#5a65a6bced85d',
                                    'position'    => 3,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'            => 'Email',
                                                    'type'             => 'operator',
                                                    'class'            => 'AnyGetter',
                                                    'attribute'        => 'email',
                                                    'param1'           => '',
                                                    'isArrayType'      => false,
                                                    'forwardAttribute' => '',
                                                    'forwardParam1'    => '',
                                                    'returnLastResult' => false,
                                                    'childs'           =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Kunde (customer)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'Href',
                                                                    'attribute' => 'customer',
                                                                    'dataType'  => 'href',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced85d',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced8d0' =>
                                [
                                    'name'        => '#5a65a6bced8d0',
                                    'position'    => 4,
                                    'hidden'      => false,
                                    'width'       => 180,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Name',
                                                    'type'   => 'operator',
                                                    'class'  => 'Concatenator',
                                                    'glue'   => ' ',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'            => 'First Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'firstname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Rechnungsadresse (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            1 =>
                                                                [
                                                                    'label'            => 'Last Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'lastname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Rechnungsadresse (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced8d0',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bcedae7' =>
                                [
                                    'name'        => '#5a65a6bcedae7',
                                    'position'    => 5,
                                    'hidden'      => false,
                                    'width'       => 145,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Gesamtpreis',
                                                    'type'   => 'operator',
                                                    'class'  => 'PriceFormatter',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Total Brutto (totalGross)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'totalGross',
                                                                    'dataType'  => 'coreShopMoney',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bcedae7',
                                        ],
                                    'isOperator'  => true,
                                ],
                            'quoteDate'      =>
                                [
                                    'name'        => 'quoteDate',
                                    'position'    => 6,
                                    'hidden'      => false,
                                    'width'       => 183,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'quoteDate',
                                            'label'  => 'Quote Date',
                                            'type'   => 'datetime',
                                            'layout' =>
                                                [
                                                    'fieldtype'       => 'datetime',
                                                    'queryColumnType' => 'bigint(20)',
                                                    'columnType'      => 'bigint(20)',
                                                    'phpdocType'      => '\\Carbon\\Carbon',
                                                    'defaultValue'    => null,
                                                    'useCurrentDate'  => false,
                                                    'name'            => 'quoteDate',
                                                    'title'           => 'Quote Date',
                                                    'tooltip'         => '',
                                                    'mandatory'       => false,
                                                    'noteditable'     => true,
                                                    'index'           => false,
                                                    'locked'          => false,
                                                    'style'           => '',
                                                    'permissions'     => null,
                                                    'datatype'        => 'data',
                                                    'relationType'    => false,
                                                    'invisible'       => false,
                                                    'visibleGridView' => false,
                                                    'visibleSearch'   => false,
                                                ],
                                            'width'  => 224,
                                        ],
                                ],
                        ],
                    'onlyDirectChildren' => false,
                    'pageSize'           => 25,
                    'pimcore_version'    => '5.1.0',
                    'pimcore_revision'   => 166,
                ],
                'en' => [
                    'language'           => 'en',
                    'sortinfo'           =>
                        [
                            'field'     => 'id',
                            'direction' => 'DESC',
                        ],
                    'classId'            => null,
                    'columns'            =>
                        [
                            'id'             =>
                                [
                                    'name'        => 'id',
                                    'position'    => 1,
                                    'hidden'      => false,
                                    'width'       => 80,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'id',
                                            'label'  => 'ID',
                                            'type'   => 'system',
                                            'layout' =>
                                                [
                                                    'title'     => 'id',
                                                    'name'      => 'id',
                                                    'datatype'  => 'data',
                                                    'fieldtype' => 'system',
                                                ],
                                        ],
                                ],
                            '#5a65a6bced7f5' =>
                                [
                                    'name'        => '#5a65a6bced7f5',
                                    'position'    => 2,
                                    'hidden'      => false,
                                    'width'       => 109,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Quote Number',
                                                    'type'   => 'operator',
                                                    'class'  => 'Trimmer',
                                                    'trim'   => 0,
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Quote Number (quoteNumber)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'quoteNumber',
                                                                    'dataType'  => 'input',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced7f5',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced85d' =>
                                [
                                    'name'        => '#5a65a6bced85d',
                                    'position'    => 3,
                                    'hidden'      => false,
                                    'width'       => 200,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'            => 'Email',
                                                    'type'             => 'operator',
                                                    'class'            => 'AnyGetter',
                                                    'attribute'        => 'email',
                                                    'param1'           => '',
                                                    'isArrayType'      => false,
                                                    'forwardAttribute' => '',
                                                    'forwardParam1'    => '',
                                                    'returnLastResult' => false,
                                                    'childs'           =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Customer (customer)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'Href',
                                                                    'attribute' => 'customer',
                                                                    'dataType'  => 'href',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced85d',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bced8d0' =>
                                [
                                    'name'        => '#5a65a6bced8d0',
                                    'position'    => 4,
                                    'hidden'      => false,
                                    'width'       => 180,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Name',
                                                    'type'   => 'operator',
                                                    'class'  => 'Concatenator',
                                                    'glue'   => ' ',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'            => 'First Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'firstname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Invoice Address (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            1 =>
                                                                [
                                                                    'label'            => 'Last Name',
                                                                    'type'             => 'operator',
                                                                    'class'            => 'AnyGetter',
                                                                    'attribute'        => 'lastname',
                                                                    'param1'           => '',
                                                                    'isArrayType'      => false,
                                                                    'forwardAttribute' => '',
                                                                    'forwardParam1'    => '',
                                                                    'returnLastResult' => false,
                                                                    'childs'           =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'label'     => 'Invoice Address (invoiceAddress)',
                                                                                    'type'      => 'value',
                                                                                    'class'     => 'Href',
                                                                                    'attribute' => 'invoiceAddress',
                                                                                    'dataType'  => 'href',
                                                                                    'childs'    =>
                                                                                        [
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bced8d0',
                                        ],
                                    'isOperator'  => true,
                                ],
                            '#5a65a6bcedae7' =>
                                [
                                    'name'        => '#5a65a6bcedae7',
                                    'position'    => 5,
                                    'hidden'      => false,
                                    'width'       => 145,
                                    'fieldConfig' =>
                                        [
                                            'isOperator' => true,
                                            'attributes' =>
                                                [
                                                    'label'  => 'Total Gross',
                                                    'type'   => 'operator',
                                                    'class'  => 'PriceFormatter',
                                                    'childs' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'label'     => 'Total Gross (totalGross)',
                                                                    'type'      => 'value',
                                                                    'class'     => 'DefaultValue',
                                                                    'attribute' => 'totalGross',
                                                                    'dataType'  => 'coreShopMoney',
                                                                    'childs'    =>
                                                                        [
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                            'key'        => '#5a65a6bcedae7',
                                        ],
                                    'isOperator'  => true,
                                ],
                            'quoteDate'      =>
                                [
                                    'name'        => 'quoteDate',
                                    'position'    => 6,
                                    'hidden'      => false,
                                    'width'       => 183,
                                    'fieldConfig' =>
                                        [
                                            'key'    => 'quoteDate',
                                            'label'  => 'Quote Date',
                                            'type'   => 'datetime',
                                            'layout' =>
                                                [
                                                    'fieldtype'       => 'datetime',
                                                    'queryColumnType' => 'bigint(20)',
                                                    'columnType'      => 'bigint(20)',
                                                    'phpdocType'      => '\\Carbon\\Carbon',
                                                    'defaultValue'    => null,
                                                    'useCurrentDate'  => false,
                                                    'name'            => 'quoteDate',
                                                    'title'           => 'Quote Date',
                                                    'tooltip'         => '',
                                                    'mandatory'       => false,
                                                    'noteditable'     => true,
                                                    'index'           => false,
                                                    'locked'          => false,
                                                    'style'           => '',
                                                    'permissions'     => null,
                                                    'datatype'        => 'data',
                                                    'relationType'    => false,
                                                    'invisible'       => false,
                                                    'visibleGridView' => false,
                                                    'visibleSearch'   => false,
                                                ],
                                            'width'  => 224,
                                        ],
                                ],
                        ],
                    'onlyDirectChildren' => false,
                    'pageSize'           => 25,
                    'pimcore_version'    => '5.1.0',
                    'pimcore_revision'   => 166,
                ],
            ]
        ];

        return $data[$type][$language];
    }
}