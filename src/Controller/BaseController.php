<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\FOSRestController;

class BaseController extends FOSRestController
{
    protected function sendMessage($message, $responseCode = Response::HTTP_NOT_FOUND)
    {
        return \FOS\RestBundle\View\View::create(['code' => $responseCode, 'message' => $message], $responseCode);
    }
}