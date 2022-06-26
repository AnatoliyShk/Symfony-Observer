<?php

namespace App\Controller;

use App\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        $entityManager = new EntityManager('/public');
        echo 123;
    }
}