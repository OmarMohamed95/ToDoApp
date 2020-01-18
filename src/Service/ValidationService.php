<?php
namespace App\Service;

class ValidationService
{
    public function identifyErrors($errors)
    {
        $violations = [];

        foreach($errors as $error)
        {
            $violations[$error->getPropertyPath()] = $error->getMessage();
        }

        return $violations;
    }

}