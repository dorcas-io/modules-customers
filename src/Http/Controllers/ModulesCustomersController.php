<?php

namespace Dorcas\ModulesCustomers\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesCustomers\Models\ModulesCustomers;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\RecordNotFoundException;

class ModulesCustomersController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-customers.title')],
            'header' => ['title' => config('modules-customers.title')],
            'selectedMenu' => 'customers',
            'submenuConfig' => 'navigation-menu.modules-customers.sub-menu',
            'submenuAction' => ''
        ];
    }

    public function main()
    {
        $this->data['submenuAction'] = '<a href="'.route("customers-new").'" class="btn btn-primary btn-block">Add Customer</a>';
    	return view('modules-customers::main', $this->data);
    }

    public function customers(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Customer Manager';
        $this->data['header']['title'] = 'Customer Manager';
        $this->data['selectedMenu'] = 'customers';
        $this->data['submenuAction'] = '<a href="'.route("customers-new").'" class="btn btn-primary btn-block">Add Customer</a>';

        $this->setViewUiResponse($request);
        $customerCount = 0;
        if ($request->has('groups')) {
            $this->data['groupFilters'] = $request->input('groups');
        }
        $response = $sdk->createCustomerResource()->addQueryArgument('limit', 1)->send('get');
        if ($response->isSuccessful()) {
            $customerCount = $response->meta['pagination']['total'] ?? 0;
        }
        $contactFields = $this->getContactFields($sdk);
        $this->data['customFields'] = [];
        if (!empty($contactFields)) {
            foreach ($contactFields as $contactField) {
                $this->data['customFields'][] = [
                    'label' => str_replace(' ', '_', strtolower($contactField->name)),
                    'title' => $contactField->name
                ];
            }
        }
        $this->data['customersCount'] = $customerCount;
        return view('modules-customers::customers.customers', $this->data);
    }



    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customers_new(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; New Customer';
        $this->data['header']['title'] = 'New Customer';
        $this->data['selectedMenu'] = 'customers';
        //$this->data['submenuAction'] = '<a href="'.route("customers-new").'" class="btn btn-primary btn-block">Add Customer</a>';

        $this->setViewUiResponse($request);
        $contactFields = $this->getContactFields($sdk);
        $this->data['contactFields'] = $contactFields;
        return view('modules-customers::customers.new', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function customers_create(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'phone' => 'required_without:email|numeric',
            'email' => 'required_without:phone|email|max:80',
            'contact_ids' => 'nullable|array',
            'contacts' => 'required_with:contact_ids|array'
        ]);
        # validate the request
        try {
            $contacts = [];
            if ($request->has('contact_ids')) {
                foreach ($request->contact_ids as $index => $fieldId) {
                    if (empty($request->contacts[$index])) {
                        continue;
                    }
                    $contacts[] = ['id' => $fieldId, 'value' => $request->contacts[$index]];
                }
            }
            $resource = $sdk->createCustomerResource();
            $resource = $resource->addBodyParam('firstname', $request->firstname)
                                    ->addBodyParam('lastname', $request->lastname)
                                    ->addBodyParam('phone', $request->input('phone', ''))
                                    ->addBodyParam('email', $request->input('email', ''));
            if (!empty($contacts)) {
                $resource = $resource->addBodyParam('fields', $contacts);
            }
            # the resource
            $response = $resource->send('post');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while saving the customer information. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully added customer.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }



    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customers_search(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $groups = $request->input('groups');

        $model = $sdk->createCustomerResource();
        $model = $model->addQueryArgument('limit', $limit)
                            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($groups)) {
            $model->addQueryArgument('groups', $groups);
        }
        if (!empty($search)) {
            $model = $model->addQueryArgument('search', $search);
        }
        $response = $model->send('get');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching customer records.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function customers_view(Request $request, Sdk $sdk, string $id)
    {
        $this->data['page']['title'] .= ' &rsaquo; Customer Profile';
        $this->data['header']['title'] = 'Customer Profile';
        $this->data['selectedMenu'] = 'customer';
        $this->data['submenuAction'] = '<a href="'.route("customers-new").'" class="btn btn-primary btn-block">Add Customer</a>';

        $this->setViewUiResponse($request);
        $response = $sdk->createCustomerResource($id)->send('get');
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the customer at this URL.');
        }
        $this->data['groups'] = $this->getGroups($sdk);
        $this->data['customer'] = $customer = $response->getData(true);
        $customerContacts = !empty($customer->contacts) ? $customer->contacts['data'] : [];
        $customerContacts = collect($customerContacts)->map(function ($contact) { return $contact['id']; })->all();
        $contactFields = $this->getContactFields($sdk);
        $this->data['availableFields'] = $contactFields->filter(function ($field) use ($customerContacts) {
            return !in_array($field->id, $customerContacts);
        });
        return view('modules-customers::customers.customer', $this->data);
    }

     /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customers_update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('firstname', $request->input('firstname'))
                            ->addBodyParam('lastname', $request->input('lastname'))
                            ->addBodyParam('email', $request->input('email'))
                            ->addBodyParam('phone', $request->input('phone'))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the customer information.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.customers.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    } 

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function customers_post(Request $request, Sdk $sdk, string $id)
    {
        $action = strtolower($request->input('action', 'save_contact_fields'));
        # get the requested action
        $this->validate($request, [
            'fields' => 'nullable|array',
            'fields.*' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*' => 'nullable|string',
            'name' => 'required_if:action,save_deal|string|max:80',
            'value_currency' => 'required_if:action,save_deal|string|size:3',
            'value_amount' => 'required_if:action,save_deal|numeric',
            'note' => 'required_if:action,save_deal|string',
        ]);
        # validate the request
        try {
            switch ($action) {
                case 'save_deal':
                    $dealId = $request->input('deal_id');
                    # check if there's a deal ID in the request
                    $resource = empty($dealId) ? $sdk->createCustomerResource($id) : $sdk->createDealResource($dealId);
                    # create the resource
                    $data = $request->only(['name', 'value_currency', 'value_amount', 'note']);
                    foreach ($data as $key => $value) {
                        $resource->addBodyParam($key, $value);
                    }
                    $response = $resource->send(empty($dealId) ? 'post' : 'put', empty($dealId) ? ['deals'] : []);
                    if (!$response->isSuccessful()) {
                        // do something here
                        throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the deal.');
                    }
                    $response = (tabler_ui_html_response(['Successfully saved the customer deal.']))->setType(UiResponse::TYPE_SUCCESS);
                    break;
                default:
                    $contacts = [];
                    foreach ($request->fields as $index => $fieldId) {
                        if (empty($request->values[$index])) {
                            continue;
                        }
                        $contacts[] = ['id' => $fieldId, 'value' => $request->values[$index]];
                    }
                    $query = $sdk->createCustomerResource($id);
                    $query = $query->addBodyParam('fields', $contacts)->send('post', ['contacts']);
                    # the query
                    if (!$query->isSuccessful()) {
                        $message = $response->errors[0]['title'] ?? '';
                        throw new \RuntimeException('Failed while saving the contact information. '.$message);
                    }
                    $response = (tabler_ui_html_response(['Successfully updated contact information.']))->setType(UiResponse::TYPE_SUCCESS);
            }
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customers_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the customer information.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.customers.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groups_customers_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createGroupResource($id)->addBodyParam('customers', $request->input('customers', []));
        $response = $model->send('delete', ['customers']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException(
                $response->errors[0]['title'] ?? 'Failed while deleting the '.
                str_plural('customer', count($request->input('customers', []))) .' from the group.'
            );
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groups_customers_add(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createGroupResource($id)->addBodyParam('customers', $request->input('customers', []));
        $response = $model->send('post', ['customers']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException(
                $response->errors[0]['title'] ?? 'Failed while adding the '.
                str_plural('customer', count($request->input('customers', []))) .' to the group.'
            );
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notes_customers_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('id', $request->input('id'))
                            ->send('delete', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the customer note.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notes_customers_read(Request $request, Sdk $sdk, string $id)
    {
        $response = $sdk->createCustomerResource($id)->addQueryArgument('limit', 10000)->send('get', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while reading the customer note.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notes_customers_add(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('note', $request->input('note'))
                            ->send('post', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the customer note.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }



    public function custom_fields(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Custom Fields';
        $this->data['header']['title'] = 'Custom Fields';
        $this->data['selectedMenu'] = 'custom-fields';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="newField" class="btn btn-primary btn-block">Add Custom Field</a>';

        $this->setViewUiResponse($request);
        $contactFields = $this->getContactFields($sdk);
        $this->data['contactFields'] = $contactFields;
        return view('modules-customers::contact-fields.contact-fields', $this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function custom_fields_create(Request $request, Sdk $sdk)
    {
        $name = $request->input('name', null);
        $model = $sdk->createContactFieldResource();
        $response = $model->addBodyParam('name', $name)
                            ->send('post');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while creating the contact field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function custom_fields_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createContactFieldResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the contact field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function custom_fields_update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createContactFieldResource($id);
        $response = $model->addBodyParam('name', $request->input('name'))->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while updating the custom field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    public function groups(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Groups';
        $this->data['header']['title'] .= ' &rsaquo; Groups';
        $this->data['selectedMenu'] = 'groups';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="createGroup" class="btn btn-primary btn-block">Add Group</a>';

        $this->setViewUiResponse($request);
        $this->data['groups'] = $this->getGroups($sdk);
        return view('modules-customers::groups', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groups_post(Request $request, Sdk $sdk)
    {
        $this->validate($request,[
            'name' => 'required|string|max:80',
            'description' => 'nullable|string'
        ]);
        # validate the request
        try {
            $groupId = $request->has('group_id') ? $request->input('group_id') : null;
            $resource = $sdk->createGroupResource($groupId);
            $payload = $request->only(['name', 'description']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send(empty($groupId) ? 'post' : 'put');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($groupId) ? 'adding' : 'updating') .' the group. '.$message);
            }
            $company = $this->getCompany();
            Cache::forget('crm.groups.'.$company->id);
            $response = (tabler_ui_html_response(['Successfully '. (empty($groupId) ? 'added' : 'updated the') .' group.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groups_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createGroupResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the group.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('crm.groups.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

}