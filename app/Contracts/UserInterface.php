<?php

namespace App\Contracts;

interface UserInterface
{
    public function getId();

    public function getPassword();

    public function getName();

    public function getRole();
}
