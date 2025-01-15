<?php

namespace App\Services;

class HelloService
{
    public function getHelloMessage()
    {
        return [
            'message' => 'Hello World'
        ];
    }
}