<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return 'CI4 Benchmark - PHP Benchmark | Endpoints: /health /compute /memory';
    }
}
