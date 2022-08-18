<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\StorageListBundle\Controller;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\DTO\AddToStorageListInterface;
use CoreShop\Component\StorageList\Factory\AddToStorageListFactoryInterface;
use CoreShop\Component\StorageList\Factory\StorageListItemFactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class StorageListController extends AbstractController
{
    protected FormFactoryInterface $formFactory;
    protected RepositoryInterface $repository;
    protected RepositoryInterface $productRepository;
    protected RepositoryInterface $itemRepository;
    protected StorageListContextInterface $context;
    protected StorageListItemFactoryInterface $storageListItemFactory;
    protected AddToStorageListFactoryInterface $addToStorageListFactory;
    protected StorageListModifierInterface $modifier;
    protected StorageListManagerInterface $manager;
    protected string $addToStorageListForm;
    protected string $form;
    protected string $summaryRoute;
    protected string $indexRoute;
    protected string $templateAddToList;
    protected string $templateSummary;

    public function __construct(
        FormFactoryInterface $formFactory,
        RepositoryInterface $repository,
        RepositoryInterface $productRepository,
        RepositoryInterface $itemRepository,
        StorageListContextInterface $context,
        StorageListItemFactoryInterface $storageListItemFactory,
        AddToStorageListFactoryInterface $addToStorageListFactory,
        StorageListModifierInterface $modifier,
        StorageListManagerInterface $manager,
        string $addToStorageListForm,
        string $form,
        string $summaryRoute,
        string $indexRoute,
        string $templateAddToList,
        string $templateSummary
    ) {
        $this->formFactory = $formFactory;
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->itemRepository = $itemRepository;
        $this->context = $context;
        $this->storageListItemFactory = $storageListItemFactory;
        $this->addToStorageListFactory = $addToStorageListFactory;
        $this->modifier = $modifier;
        $this->manager = $manager;
        $this->addToStorageListForm = $addToStorageListForm;
        $this->form = $form;
        $this->summaryRoute = $summaryRoute;
        $this->indexRoute = $indexRoute;
        $this->templateAddToList = $templateAddToList;
        $this->templateSummary = $templateSummary;
    }


    public function addItemAction(Request $request): Response
    {
        $redirect = $request->get('redirect');
        $product = $this->productRepository->find($request->get('product'));
        $storageList = $this->context->getStorageList();

        if (!$product instanceof ResourceInterface) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            return $this->redirect($redirect);
        }

        $item = $this->storageListItemFactory->createWithStorageListProduct($product);

        $addToStorageList = $this->createAddToStorageList($storageList, $item);

        $form = $this->formFactory->createNamed(
            'coreshop-'.$product->getId(),
            $this->addToStorageListForm,
            $addToStorageList
        );

        if ($request->isMethod('POST')) {
            $redirect = $request->get('_redirect', $this->generateUrl($this->summaryRoute));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var AddToStorageListInterface $addToStorageList
                 */
                $addToStorageList = $form->getData();

                $this->modifier->addToList($addToStorageList->getStorageList(), $addToStorageList->getStorageListItem());
                $this->manager->persist($storageList);

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_added'));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'success' => true,
                    ]);
                }

                return $this->redirect($redirect);
            }

            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => array_map(static function (FormError $error) {
                        return $error->getMessage();
                    }, iterator_to_array($form->getErrors(true))),
                ]);
            }

            return $this->redirect($redirect);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        return $this->render(
            $request->get('template', $this->templateAddToList),
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }

    public function removeItemAction(Request $request): Response
    {
        /**
         * @var StorageListItemInterface $storageListItem
         */
        $storageListItem = $this->itemRepository->find($request->get('item'));
        $storageList = $this->context->getStorageList();

        if (!$storageListItem instanceof StorageListItemInterface) {
            return $this->redirectToRoute($this->indexRoute);
        }

        if ($storageListItem->getStorageList()->getId() !== $storageList->getId()) {
            return $this->redirectToRoute($this->indexRoute);
        }

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_removed'));

        $this->modifier->removeFromList($storageList, $storageListItem);
        $this->manager->persist($storageList);

        return $this->redirectToRoute($this->summaryRoute);
    }

    public function summaryAction(Request $request): Response
    {
        $list = $this->context->getStorageList();
        $form = $this->formFactory->createNamed('coreshop', $this->form, $list);
        $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isSubmitted()) {
            if ($form->isValid()) {
                $list = $form->getData();

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.cart_updated'));

                $this->manager->persist($list);

                return $this->redirectToRoute($this->summaryRoute);
            }

            $session = $request->getSession();

            if ($session instanceof Session) {
                foreach ($form->getErrors() as $error) {
                    $session->getFlashBag()->add('error', $error->getMessage());
                }

                return $this->redirectToRoute($this->summaryRoute);
            }
        }

        return $this->render($this->templateSummary, [
            'storage_list' => $list,
            'form' => $form->createView(),
        ]);
    }

    protected function createAddToStorageList(
        StorageListInterface $storageList,
        StorageListItemInterface $storageListItem
    ): AddToStorageListInterface {
        return $this->addToStorageListFactory->createWithStorageListAndStorageListItem($storageList, $storageListItem);
    }
}
