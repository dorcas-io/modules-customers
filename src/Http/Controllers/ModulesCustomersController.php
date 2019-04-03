<?php

namespace Dorcas\ModulesCustomers\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesCustomers\Models\ModulesCustomers;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;

class ModulesCustomersController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-customers.title')],
            'header' => ['title' => config('modules-customers.title')],
            'selectedMenu' => 'customers'
        ];
    }

    public function index()
    {
    	//$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-customers::index', $this->data);
    }


}