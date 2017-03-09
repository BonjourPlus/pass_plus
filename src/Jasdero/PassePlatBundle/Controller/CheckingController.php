<?php

namespace Jasdero\PassePlatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class CheckingController extends Controller
{


    /**
     * Used to check that a user exists
     *
     * @param string $email A user mail
     * @return Response
     */
    public function validateUser($email)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JasderoPassePlatBundle:User')->findOneBy(['email' => $email]);
        $response = $user;
        if (!$user) {
            $response = false;
        }

        return ($response);
    }

    //create a function to check that an order is valid : must have at least one product

    public function validateOrder(array $products)
    {
        $em = $this->getDoctrine()->getManager();
        $response = true;

        foreach ($products as $product) {
            if(!$match=$em->getRepository('JasderoPassePlatBundle:Catalog')->findOneBy(['id'=>$product]) ){
                $response = false;
            }
        }
        return ($response);

    }
}
