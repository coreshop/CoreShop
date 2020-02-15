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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CompanyRepositoryInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerTransformerController extends AdminController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkForNameDuplicatesAction(Request $request)
    {
        $error = false;
        $message = null;
        $objects = [];
        $foundObjects = [];
        $value = $request->query->get('value', null);

        if ($value !== null) {
            $list = $this->getCompanyRepository()->getList();
            $list->addConditionParam(sprintf('name LIKE "%%%s%%"', $value));
            $foundObjects = $list->getObjects();
        }

        /** @var CompanyInterface $maybeDuplicate */
        foreach ($foundObjects as $maybeDuplicate) {
            $objects[] = [
                'id'   => $maybeDuplicate->getId(),
                'name' => $maybeDuplicate->getName(),
                'path' => $maybeDuplicate->getFullPath()
            ];
        }

        return $this->json([
            'success' => !$error,
            'message' => $message,
            'list'    => $objects
        ]);
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param int     $objectId
     *
     * @return JsonResponse
     */
    public function getEntityDetailsAction(Request $request, string $type, $objectId)
    {
        $error = false;
        $message = null;
        $data = null;

        $object = $type === 'customer' ? $this->getCustomerRepository()->find($objectId) : $this->getCompanyRepository()->find($objectId);

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

        return $this->json([
            'success' => !$error,
            'message' => $message,
            'data'    => $data
        ]);
    }

    /**
     * @param Request  $request
     * @param int      $customerId
     * @param int|null $companyId
     *
     * @return JsonResponse
     */
    public function validateAssignmentAction(Request $request, $customerId, $companyId)
    {
        $error = false;
        $message = null;
        $data = null;

        $customer = $this->getCustomerRepository()->find($customerId);
        $company = $companyId === null ? null : $this->getCompanyRepository()->find($companyId);

        if (!$customer instanceof CustomerInterface) {
            $error = true;
            $message = 'Invalid Customer Object. Please choose a valid customer.';

            return $this->json([
                'success' => !$error,
                'message' => $message,
                'data'    => $data
            ]);
        }

        if ($companyId !== null && !$company instanceof CompanyInterface) {
            $error = true;
            $message = 'Invalid Company Object. Please choose a valid company.';

            return $this->json([
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

        return $this->json([
            'success' => !$error,
            'message' => $message,
            'data'    => $data
        ]);
    }

    /**
     * @param Request $request
     * @param int     $customerId
     * @param int     $companyId
     *
     * @return JsonResponse
     */
    public function dispatchExistingAssignmentAction(Request $request, $customerId, $companyId)
    {
        $error = false;
        $formError = false;
        $message = null;
        $data = null;

        $addressAssignmentType = $request->get('addressAssignmentType');
        $addressAccessType = $request->get('addressAccessType');

        /** @var CustomerInterface $customer */
        $customer = $this->getCustomerRepository()->find($customerId);

        /** @var CompanyInterface $company */
        $company = $this->getCompanyRepository()->find($companyId);

        $options = [
            'addressAssignmentType' => $addressAssignmentType,
            'addressAccessType'     => $addressAccessType,
        ];

        try {
            $this->getCustomerTransformerHelper()->moveCustomerToExistingCompany($customer, $company, $options);
        } catch (ValidationException $e) {
            $error = true;
            $formError = true;
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        return $this->json([
            'success'    => !$error && !$formError,
            'formError'  => $formError,
            'message'    => $message,
            'customerId' => $customerId,
            'companyId'  => $companyId
        ]);
    }

    /**
     * @param Request $request
     * @param int $customerId
     *
     * @return JsonResponse
     */
    public function dispatchNewAssignmentAction(Request $request, $customerId)
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
        $customer = $this->getCustomerRepository()->find($customerId);

        $options = [
            'addressAssignmentType' => $addressAssignmentType,
            'addressAccessType'     => $addressAccessType,
            'companyData'           => [
                'name' => $newCompanyName
            ]
        ];

        try {
            $this->getCustomerTransformerHelper()->moveCustomerToNewCompany($customer, $options);

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

        return $this->json([
            'success'    => !$error && !$formError,
            'formError'  => $formError,
            'message'    => $message,
            'customerId' => $customerId,
            'companyId'  => $companyId
        ]);
    }

    /**
     * @return CustomerRepositoryInterface
     */
    protected function getCustomerRepository()
    {
        return $this->get('coreshop.repository.customer');
    }

    /**
     * @return CompanyRepositoryInterface
     */
    protected function getCompanyRepository()
    {
        return $this->get('coreshop.repository.company');
    }

    /**
     * @return CustomerTransformHelperInterface
     */
    protected function getCustomerTransformerHelper()
    {
        return $this->get(CustomerTransformHelperInterface::class);
    }
}
