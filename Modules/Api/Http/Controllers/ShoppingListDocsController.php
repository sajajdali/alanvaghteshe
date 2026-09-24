<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ShoppingListDocsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        return view('api::shopping-list-docs');
    }

    public function specification(): Response
    {
        return response(
            file_get_contents(module_path('Api', 'Docs/shopping-list-openapi.yaml')),
            200,
            ['Content-Type' => 'application/yaml; charset=UTF-8']
        );
    }
}
