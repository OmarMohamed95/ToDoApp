<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Base controller
 */
abstract class BaseController extends AbstractFOSRestController
{
    /**
     * Respond to API
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return Response
     */
    public function respond($data = null, int $statusCode = 200, array $headers = [])
    {
        $view = $this->view($data, $statusCode, $headers);
        return $this->handleView($view);
    }

    /**
     * Respond with no content
     *
     * @return Response
     */
    public function respondNoContent()
    {
        $view = $this->view(null, Response::HTTP_NO_CONTENT);
        return $this->handleView($view);
    }
}
