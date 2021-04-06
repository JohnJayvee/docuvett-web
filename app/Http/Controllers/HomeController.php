<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        return view('layouts.app');
    }

    /**
     * @return Application|ResponseFactory|Response
     * @throws Throwable
     */
    public function serviceWorker()
    {
        return response(view('includes.service-worker')->render())->header('Content-Type', 'application/javascript');
    }

    /**
     * @return Application|ResponseFactory|Response
     * @throws Throwable
     */
    public function commonJsVariables()
    {
        return response(view('includes.common-js-variables')->render())->header('Content-Type', 'application/javascript');
    }
}
