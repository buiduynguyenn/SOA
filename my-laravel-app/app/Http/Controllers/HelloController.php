<?php

namespace App\Http\Controllers;

use App\Services\HelloService;
use Illuminate\Http\Request;

class HelloController extends Controller
{
    protected $helloService;

    public function __construct(HelloService $helloService)
    {
        $this->helloService = $helloService;
    }

    public function index()
    {
        return response()->json(
            $this->helloService->getHelloMessage()
        );
    }
}
