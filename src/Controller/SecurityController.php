<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("eshop/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //if ($this->getUser()) {
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))   {     
            $session = $this->get('session');
            $session->set('msgerror','7');        //'Please logout first! 
            return $this->redirectToRoute('message' );
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
      
        $data['menu_register'] = ['url' => "register",
            'text' => "Register",                 
            'icon' => 'glyphicon glyphicon glyphicon-user'   ];
        $data['menu_home'] = ['url' => "products",      
            'brand' => "Algarve Sunseekers" ,   
            'business' =>  "Freedom charters", 
            'motto' =>"<i>Just the fun, no ownership hassles</i>" ];


        dump($data);
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'menu_register' => $data['menu_register'],
            'menu_home' => $data['menu_home']
            ]);

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

