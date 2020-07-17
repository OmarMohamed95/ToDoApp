<?php
namespace App\Service;

/**
 * ValidationService
 */
class ValidationService
{
    /**
     * Identify errors
     *
     * @param array $errors
     *
     * @return array
     */
    public function identifyErrors(array $errors): array
    {
        $violations = [];

        foreach ($errors as $error) {
            $violations[$error->getPropertyPath()] = $error->getMessage();
        }

        return $violations;
    }
}
