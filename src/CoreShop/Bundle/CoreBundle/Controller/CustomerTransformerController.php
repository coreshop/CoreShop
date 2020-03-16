<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CompanyRepositoryInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerTransformerController extends AdminController
{
    public function checkForNameDuplicatesAction(
        Request $request,
        CompanyRepositoryInterface $companyRepository,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $error = false;
        $message = null;
        $objects = [];
        $foundObjects = [];
        $value = $request->query->get('value', null);

        if ($value !== null) {
            $list = $companyRepository->getList();
            $list->addConditionParam(sprintf('name LIKE "%%%s%%"', $value));
            $foundObjects = $list->getData();
        }

        /** @var CompanyInterface $maybeDuplicate */
        foreach ($foundObjects as $maybeDuplicate) {
            $objects[] = [
                'id'   => $maybeDuplicate->getId(),
                'name' => $maybeDuplicate->getName(),
                'path' => $maybeDuplicate->getFullPath()
            ];
        }

        return $viewHandler->handle([
            'success' => !$error,
            'message' => $message,
            'list'    => $objects
        ]);
    }

    public function getEntityDetailsAction(
        CustomerRepositoryInterface $customerRepository,
        CompanyRepositoryInterface $companyRepository,
        ViewHandlerInterface $viewHandler,
        string $type,
        int $objectId
    ): Response
    {
        $error = false;
        $message = null;
        $data = null;

        $object = $type === 'customer' ? $customerRepository->find($objectId) : $companyRepository->find($objectId);

        if ($type === 'customer') {
            if (!$object instanceof CustomerInterface) {
                $error = true;
                $message = 'Invalid Customer Object. Please choose a valid customer.';
            } else {
                $data = [
                    'type' => $type,
                    'name' => sprintf('%s %s', $object->getFirstname(), $object->getLastname()),
                    'id'   => $object->getId()
                ];
            }
        } elseif ($type === 'company') {
            if (!$object instanceof CompanyInterface) {
                $error = true;
                $message = 'Invalid Customer Object. Please choose a valid company.';
            } else {
                $data = [
                    'type' => $type,
                    'name' => sprintf('%s', $object->getName()),
                    'id'   => $object->getId()
                ];
            }
        }

        return $viewHandler->handle([
            'success' => !$error,
            'message' => $message,
            'data'    => $data
        ]);
    }

    public function validateAssignmentAction(
        CustomerRepositoryInterface $customerRepository,
        CompanyRepositoryInterface $companyRepository,
        ViewHandlerInterface $viewHandler,
        int $customerId,
        int $companyId = null): Response
    {
        $error = false;
        $message = null;
        $data = null;

        $customer = $customerRepository->find($customerId);
        $company = $companyId === null ? null : $companyRepository->find($companyId);

        if (!$customer instanceof CustomerInterface) {
            $error = true;
            $message = 'Invalid Customer Object. Please choose a valid customer.';

            return $viewHandler->handle([
                'success' => !$error,
                'message' => $message,
                'data'    => $data
            ]);
        }

        if ($companyId !== null && !$company instanceof CompanyInterface) {
            $error = true;
            $message = 'Invalid Company Object. Please choose a valid company.';

            return $viewHandler->handle([
                'success' => !$error,
                'message' => $message,
                'data'    => $data
            ]);
        }

        if ($customer->getCompany() instanceof CompanyInterface) {
            $error = true;
            $message = sprintf('Customer already assigned to company ("%s"). Cannot proceed.', $customer->getCompany()->getName());
        }

        $availableCustomerAddresses = [];

        if ($error === false) {
            foreach ($customer->getAddresses() as $address) {
                $availableCustomerAddresses[] = [
                    'id'   => $address->getId(),
                    'path' => $address->getFullPath(),
                ];
            }

            $data = [
                'addresses' => $availableCustomerAddresses
            ];
        }

        return $viewHandler->handle([
            'success' => !$error,
            'message' => $message,
            'data'    => $data
        ]);
    }

    public function dispatchExistingAssignmentAction(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        CompanyRepositoryInterface $companyRepository,
        CustomerTransformHelperInterface $customerTransformHelper,
        ViewHandlerInterface $viewHandler,
        $customerId,
        $companyId
    )
    {
        $error = false;
        $formError = false;
        $message = null;
        $data = null;

        $addressAssignmentType = $request->get('addressAssignmentType');
        $addressAccessType = $request->get('addressAccessType');

        /** @var CustomerInterface $customer */
        $customer = $customerRepository->find($customerId);

        /** @var CompanyInterface $company */
        $company = $companyRepository->find($companyId);

        $options = [
            'addressAssignmentType' => $addressAssignmentType,
            'addressAccessType'     => $addressAccessType,
        ];

        try {
            $customerTransformHelper->moveCustomerToExistingCompany($customer, $company, $options);
        } catch (ValidationException $e) {
            $error = true;
            $formError = true;
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        return $viewHandler->handle([
            'success'    => !$error && !$formError,
            'formError'  => $formError,
            'message'    => $message,
            'customerId' => $customerId,
            'companyId'  => $companyId
        ]);
    }

    public function dispatchNewAssignmentAction(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        CustomerTransformHelperInterface $customerTransformHelper,
        ViewHandlerInterface $viewHandler,
        $customerId
    ): Response
    {
        $error = false;
        $formError = false;
        $message = null;
        $data = null;

        $companyId = null;

        $addressAssignmentType = $request->get('addressAssignmentType');
        $addressAccessType = $request->get('addressAccessType');
        $newCompanyName = $request->get('newCompanyName');

        /** @var CustomerInterface $customer */
        $customer = $customerRepository->find($customerId);

        $options = [
            'addressAssignmentType' => $addressAssignmentType,
            'addressAccessType'     => $addressAccessType,
            'companyData'           => [
                'name' => $newCompanyName
            ]
        ];

        try {
            $customerTransformHelper->moveCustomerToNewCompany($customer, $options);

            $customerId = $customer->getId();
            $companyId = $customer->getCompany()->getId();

        } catch (ValidationException $e) {
            $error = true;
            $formError = true;
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        return $viewHandler->json([
            'success'    => !$error && !$formError,
            'formError'  => $formError,
            'message'    => $message,
            'customerId' => $customerId,
            'companyId'  => $companyId
        ]);
    }
}
