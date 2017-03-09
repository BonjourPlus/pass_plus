<?php

namespace Jasdero\PassePlatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract  class CheckingController extends Controller
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
        $user = $em->getRepository('JasderoPassePlatBundle:User')->findOneBy(['email'=>$email]);

        if(!$user){
            throw New AccessDeniedException('Access Denied');
        }

        return ($user);
    }
    
    //create a function to check that an order is valid : must have at least one product

    public function validateOrder()
    {

    }
}
