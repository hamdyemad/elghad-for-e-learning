<?php

namespace App\Exceptions;

use Exception;

class CategoryAlreadyExistsException extends Exception
{
    protected $code = 409;
    protected $message = 'Category with this slug already exists';
}
