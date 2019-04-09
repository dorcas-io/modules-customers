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
            'submenuConfig' => 'navigation-menu.modules-customers.sub-menu'
        ];
    }

    public function main()
    {
    	return view('modules-customers::crm', $this->data);
    }

    public function customers(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Customer Manager';
        $this->data['header']['title'] .= ' &rsaquo; Customer Manager';
        $this->data['selectedMenu'] = ' customers';
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

    public function custom_fields(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Custom Fields';
        $this->data['header']['title'] .= ' &rsaquo; Custom Fields';
        $this->data['selectedMenu'] = 'custom-fields';

        $this->setViewUiResponse($request);
        return view('modules-customers::contact-fields.contact-fields', $this->data);
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