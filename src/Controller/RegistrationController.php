<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/eshop/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))   {     
            $session = $this->get('session');
            $session->set('msgerror','7');        //'Please logout first! 
            return $this->redirectToRoute('message' );
        }
        $user = new Customers();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $data['menu_login'] = ['url' => "login",
            'text' => "Login",                 
            'icon' => 'glyphicon glyphicon glyphicon-log-in'   ];
        $data['menu_home'] = ['url' => "products",      
            'brand' => "Algarve Sunseekers" ,   
            'business' =>  "Freedom charters", 
            'motto' =>"<i>Just the fun, no ownership hassles</i>" ];
        dump($user, $data);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPasswordDigest(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setName($form->get('name')->getData());
            $present_date =  new \DateTime('now');//"teste";//new \DateTime();
            $user->setCreatedAt($present_date);
            $user->setUpdatedAt($present_date);
         
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
               

            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('eshop/register.html.twig', [
            'registrationForm' => $form->createView(), 
            'menu_login' => $data['menu_login'],
            'menu_home' => $data['menu_home']
        ]);
    }
}
