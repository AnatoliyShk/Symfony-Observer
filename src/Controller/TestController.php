<?php

namespace App\Controller;

use App\Helper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{

    private $mailerDSN;

    public function __construct(string $mailerDSN)
    {
        $this->mailerDSN = $mailerDSN;
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        Helper::driver();
        return new Response();
    }
}