<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;

abstract class Base extends TestCase
{
    /**
     * Print Test Name.
     */
    public function printTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            echo '### running ...  '.$trace[1]['class'].'::'.$trace[1]['function']." ... good luck!\n"; //get the class and function name when running phpunit from CoreShop/tests directory
        }
    }

    /**
     * Print TO-DO Test Name.
     */
    public function printTodoTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            echo '### running ...  '.$trace[1]['class'].'::'.$trace[1]['function']." ... good luck! TODO! \n"; //get the class and function name when running phpunit from CoreShop/tests directory
        }
    }

    /**
     * Setup Test.
     */
    public function setUp()
    {
        //Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $class
     *
     * @return FactoryInterface
     */
    protected function getFactory($class)
    {
        $factory = $this->get('coreshop.factory.'.$class);

        if (!$factory instanceof FactoryInterface) {
            throw new \InvalidArgumentException(sprintf('%s factory class does not exist or is wrong configured', $class));
        }

        return $factory;
    }

    /**
     * @param $class
     *
     * @return RepositoryInterface
     */
    protected function getRepository($class)
    {
        $repo = $this->get('coreshop.repository.'.$class);

        if (!$repo instanceof RepositoryInterface) {
            throw new \InvalidArgumentException(sprintf('%s repository class does not exist or is wrong configured', $class));
        }

        return $repo;
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->get('form.factory');
    }

    /**
     * @param $alias
     *
     * @return MetadataInterface
     */
    protected function getMetadata($alias)
    {
        return $this->get('coreshop.resource_registry')->get($alias);
    }

    /**
     * @param $resourceAlias
     * @param $expectedClass
     * @param $data
     *
     * @return ResourceInterface
     */
    protected function createResourceWithForm($resourceAlias, $expectedClass, $data)
    {
        $metadata = $this->getMetadata('coreshop.'.$resourceAlias);
        $formType = $metadata->getClass('form');

        $form = $this->getFormFactory()->createNamed('', $formType);

        $form->submit($data);

        $resourceModel = $form->getData();

        $this->assertInstanceOf($expectedClass, $resourceModel);

        return $resourceModel;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    protected function get($id)
    {
        return \Pimcore::getKernel()->getContainer()->get($id);
    }
}
