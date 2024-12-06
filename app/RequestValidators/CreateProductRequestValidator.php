<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class CreateProductRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name', 'price', 'stock_quantity', 'category']); 
        $v->rule('lengthMax', 'name', 255);
        $v->rule('lengthMax', 'description', 1000); 
        $v->rule('numeric', 'price');
        $v->rule('min', 'price', 0.01);
        $v->rule('integer', 'stock_quantity');
        $v->rule('min', 'stock_quantity', 0);

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
