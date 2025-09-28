<?php

namespace tests;

class TestUser
{
    public $id = 42;
    public $name = "Alice Johnson";
    public $email = "alice@example.com";
    public $company;

    public function __construct()
    {
        $this->company = new TestCompany();
    }
}

class TestCompany
{
    public $id = 100;
    public $name = "Tech Corp";
}