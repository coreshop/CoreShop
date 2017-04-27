<?php

namespace CoreShop\Test;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * Print Test Name
     */
    public function printTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            print("### running ...  " . $trace[1]["class"] . "::" . $trace[1]["function"] . " ... good luck!\n"); //get the class and function name when running phpunit from CoreShop/tests directory
        }
    }

    /**
     * Print Todo Test Name
     */
    public function printTodoTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            print("### running ...  " . $trace[1]["class"] . "::" . $trace[1]["function"] . " ... good luck! TODO! \n"); //get the class and function name when running phpunit from CoreShop/tests directory
        }
    }

    /**
     * Setup Test
     */
    public function setUp()
    {
        //Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $class
     * @return FactoryInterface
     */
    protected function getFactory($class) {
        $factory = $this->get('coreshop.factory.' . $class);

        if (!$factory instanceof FactoryInterface) {
            throw new \InvalidArgumentException(sprintf('%s factory class does not exist or is wrong configured', $class));
        }

        return $factory;
    }

    /**
     * @param $class
     * @return RepositoryInterface
     */
    protected function getRepository($class) {
        $repo = $this->get('coreshop.repository.' . $class);

        if (!$repo instanceof RepositoryInterface) {
            throw new \InvalidArgumentException(sprintf('%s repository class does not exist or is wrong configured', $class));
        }

        return $repo;
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory() {
        return $this->get('form.factory');
    }

    /**
     * @param $alias
     * @return MetadataInterface
     */
    protected function getMetadata($alias) {
        return $this->get('coreshop.resource_registry')->get($alias);
    }

    /**
     * @param $resourceAlias
     * @param $expectedClass
     * @param $data
     * @return ResourceInterface
     */
    protected function createResourceWithForm($resourceAlias, $expectedClass, $data) {
        $metadata = $this->getMetadata('coreshop.' . $resourceAlias);
        $formType = $metadata->getClass('form');

        $form = $this->getFormFactory()->createNamed('', $formType);

        $form->submit($data);

        $resourceModel = $form->getData();

        $this->assertInstanceOf($expectedClass, $resourceModel);

        return $resourceModel;
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function get($id) {
        return \Pimcore::getKernel()->getContainer()->get($id);
    }
}
