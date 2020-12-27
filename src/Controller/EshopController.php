<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
//use App\Controller\Blog_model;
use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;

class EshopController extends AbstractController
{
	//private $eshop_model;
	private $session;
	private $validator;

	public function __construct( SessionInterface $session,ValidatorInterface $validator)
	{
		$this->session = $session;
		$this->validator = $validator;
        
	}


   /**
     
     * @Route("/eshop/products/{id?}", name="products")
     */
    public function index($id = FALSE, ProductsRepository $productsRepository, CategoriesRepository $categoriesRepository): Response
    {


    //	$remember_digest = $request->cookies->get('siteAuth');
   	// if ($remember_digest){  
    //      // if rememberMe cookie is set, autologin
    //      $user = $this->eshop_model->check_remember_digest($remember_digest);

   	// 	if ($user){
   	// 		$this->session->set('name', $user['name']);
   	// 		$this->session->set('email', $user['email']);
   	// 		$this->session->set('userid', $user['id']);

   	// 	}
   	// }

        $product = $productsRepository   ->get_products($id);
        $catlist = $categoriesRepository->get_categories();
        if (!$product) {
            throw $this->createNotFoundException(   'No product found for id '.$id         );
        }

        $data['products'] = $product; 
        $data['categories'] = $catlist; 
        $data['message'] = "Hello! This is the welcome message.";      
        $data['menu_home'] = ['url' => "products",      
            'brand' => "Algarve Sunseekers" ,   
            'business' =>  "Freedom charters", 
            'motto' =>"<i>Just the fun, no ownership hassles</i>" ];

        if ( $this->session->get('userid') )
        {   
            $data['sessionID'] = $this->session->get('userid') ;          
            $data['menu_welcome'] = [ 'text' => 'Welcome '. $this->session->get('name')          . 'app.user' . app.user                      ]; 
            $data['menu_login'] = ['url' => "logout",    'text' => "Logout",                                    'icon' => 'glyphicon glyphicon glyphicon-log-out'  ];  
        }
        else
        {   
            $data['menu_welcome'] = ['url' => "",          'text' => "Welcome",                                   'icon' => 'glyphicon glyphicon-thumbs-up'  ];
            $data['menu_login'] = ['url' => "login",     'text' => "Login",                                     'icon' => 'glyphicon glyphicon glyphicon-log-in'   ];
            $data['menu_register'] = ['url' => "register",  'text' => "Register",                                  'icon' => "glyphicon glyphicon glyphicon-user"     ];
        } 


        $data['cart'] = ['total' => "0.00",   'quantity' => "0"  ];
        $this->session->set('cart', "");
         dump($this, $data, $this->getUser()); 
        return $this->render('eshop/products.html.twig',$data);
    }



	 /**
     * @Route("/eshop/add_product/{id}", name="addproduct") 
     */
    public function addproduct($id = FALSE)
    { 

    }


    public function getcategories(CategoriesRepository $categoriesRepository): Response
    {
        // get the recent articles somehow (e.g. making a database query)
        $catlist = $categoriesRepository->get_categories();
        return $this->render('eshop/_categories.html.twig', ['categories' => $catlist  ]);
    }







    // /**
    // * @Route("/eshop/register", name="register")
    // */   
    // public function register() 
    // {     
    //     if ( $this->session->get('userid') )
    //         return $this->redirectToRoute('eshop');
    //     $data['menu_home'] =    ['url' => "eshop",  'text' => "Home",     'icon' => "glyphicon glyphicon-home"               ]; 
    //     $data['menu_login'] =   ['url' => "login",  'text' => "Login",    'icon' => "glyphicon glyphicon glyphicon-log-in"   ];
    //     $data['email'] = '';
    //     $data['username'] = '';
    //     $data['errors'] = 0;
    //     $data['message'] = " Hello! This is the register message.";
    //     $data['panel_contextual_class'] = 'panel-info' ;
    //     return $this->render('eshop/register_template.html.twig', $data);
    // }




    /**
    * @Route("/eshop/register_action", name="register_action")
    */   
    public function register_action(Request $request, ValidatorInterface $validator)
    {     
        if ( $this->session->get('userid') )
            return $this->redirectToRoute('eshop');

        $token = $request->request->get("token");
        if (!$this->isCsrfTokenValid('register_form', $token)) {
           return new Response("Operation not allowed", Response::HTTP_OK, ['content-type' => 'text/plain']);
        }
        $username        = $request->request->get('username');
        $email           = $request->request->get('email');
        $password        = $request->get('password');
        $retypedPassword = $request->get('retypedPassword');

        $user = $this->eshop_model->get_user($email);
        $value = ( $user == false ? '' : $user['email'] );

        $input = ['email' => $email, 'password' => $password, 'retypedPassword' => $retypedPassword, 'username' => $username];

        $constraints = new Assert\Collection([
            'username' =>          [new Assert\NotBlank(['message'=>"Username  cannot be empty!"]),
            new Assert\Length(['min' => 4, 'minMessage' => 'Your name must be at least {{ limit }} characters long'])],
            'email' =>             [new Assert\NotBlank, new Assert\Email([ 'message' => 'The email "{{ value }}" is not a valid email']),
            new Assert\NotEqualTo(['value' => $value, 'message' => "This email is already in the database"])],
            'password' =>          [new Assert\NotBlank(['message'=>"Password cannot be empty!"]),
            new Assert\EqualTo(['value' => $retypedPassword, 'message' => "Passwords do not match"])],
            'retypedPassword' =>   [new Assert\NotBlank(['message' => "Password Confirmation must not be blank"])],             
        ]);

        $data = $this->requestValidation($input, $constraints);

        // if registering rules not observed
        if ( $data['errors'] > 0) {
            $data['menu_home'] = ['url' => "eshop",   'text' => "Home",     'icon' => "glyphicon glyphicon-home"               ]; 
            $data['menu_login'] = ['url' => "login",  'text' => "Login",    'icon' => "glyphicon glyphicon glyphicon-log-in"   ]; 

            $data['email'] = $email;
            $data['username'] = $username;
            $data['panel_contextual_class'] = 'panel-warning';  

            return $this->render('eshop/register_template.html.twig', $data);
        }
          // if no errors
        $this->eshop_model->register_user($username,$email,$password);
        $this->session->set('msgerror','16');
        return $this->redirectToRoute('message');          
    }





    // /**
    // * @Route("/eshop/login", name="login")
    // */   
    // public function login() 
    // {
    //     $data['email'] = '';     
    //     if ( $this->session->get('userid') ){
    //         $this->session->set('msgerror','7');      //'Please logout first! 
    //         return $this->redirectToRoute('message');    
    //     }
    //     $data['menu_home'] =    ['url' => "products",  'text' => "Home",        'icon' => "glyphicon glyphicon-home"            ];
    //     $data['menu_welcome'] = ['url' => "",          'text' => "Welcome",     'icon' => ''                                    ];
    //     $data['menu_login'] = "";
    //     $data['menu_register'] =       ['url' => "register",  'text' => "Register",    'icon' => "glyphicon glyphicon glyphicon-user"  ];
    //     $data['email'] = '';
    //     $data['password'] = '';
    //     $data['errors'] = 0;
    //     $data['panel_contextual_class'] = 'panel-info' ;
    //     $data['message'] = "Hello! This is the login message.";  
    //     return $this->render('eshop/login.html.twig', $data);
    // }




    /**
    * @Route("/eshop/login_action", name="login_action")
    */
    public function login_action(Request $request, ValidatorInterface $validator)
    {  
        if ( $this->session->get('userid') )
            return $this->redirectToRoute('eshop');
        $token = $request->request->get("token");
        if (!$this->isCsrfTokenValid('login_form', $token)) {
            return new Response("Operation not allowed", Response::HTTP_OK, ['content-type' => 'text/plain']);
        }
        $email    = $request->request->get('email');
        $password = $request->get('password');        

        $user = $this->eshop_model->login($email,$password);

        $value = ( $user == false ? '' : $password ); 

        $input = ['email' => $email, 'password' => $password];
        $constraints = new Assert\Collection([             
            'email' =>             [new Assert\NotBlank([                    'message' => "Email cannot be empty!"]), 
            new Assert\Email(   [                    'message' => 'The email "{{ value }}" is not a valid email'])],
            'password' =>          [new Assert\NotBlank([                    'message' => "Password cannot be empty!"]),
            new Assert\EqualTo( ['value' => $value,  'message' => "Wrong username or password"])]                      
        ]);
        $data = $this->requestValidation($input, $constraints);

        $data['email'] = $email;
        $data['message'] = "Hello! This is the login message."; 
        $data['menu_home'] =    ['url' => "eshop",     'text' => "Home",     'icon' => "glyphicon glyphicon-home"            ]; 
        $data['menu_welcome'] = ['url' => "",          'text' => "Welcome",  'icon' => ''                                    ];
        $data['menu_register'] =['url' => "register",  'text' => "Register", 'icon' => "glyphicon glyphicon glyphicon-user"  ];        

          // if registering rules not observed
        if ($data['errors'] > 0) {    
            $data['email'] = $email;
            $data['password'] = '';
            $data['panel_contextual_class'] = 'panel-warning';                  
            return $this->render('eshop/login_template.html.twig', $data);
        }
        $this->session->set('userid', $user['id']);
        $this->session->set('name', $user['name']);      
        $this->session->set('email', $user['email']);

          //Create cookie if remember me is set
        if ($request->get('autologin') == 1){ 
            $remember_digest = substr(md5(time()),0,32);
            $this->cookie_set($remember_digest);        
        }
        $this->session->set('msgerror','1');      //'Welcome back!
        return $this->redirectToRoute('message');    
    }



    /**
    * @Route("/eshop/logout", name="logout")
    */
    public function logout()
    {             
        // zero and expire cookie 
        //setcookie('siteAuth', '0', time() - (3600*24));
        $this->cookie_reset();
        // unset  session variables
        $this->session->set('userid', '');
        $this->session->set('name', '');
        $user_email = $this->session->get('email');
        $this->session->set('email', '');
        $this->session->clear();
        // update DB remember digest
        $this->eshop_model->logout_user($user_email)   ;
        $this->session->set('msgerror','2');      // 'See you back soon!' 
        return $this->redirectToRoute('message');      
    }



    /**
    * @Route("/eshop/post/{eshop_id?}", name="post")
    */
    public function post($eshop_id = FALSE)
    {
        $data['menu_home'] = ['url' => "eshop",      'text' => "Home",     'icon' => "glyphicon glyphicon-home" ]; 
        $data['message'] = "Hello! This is the eshop message."; 
        // if updating a eshop post
        if ($eshop_id) { 
            $post =  $this->eshop_model->get_eshop($eshop_id);
            $data['eshop'] =$post['content'];
            $data['eshop_id'] = $eshop_id;
            // if not author
            if ($this->session->get('userid') != $post['user_id']) {   
                $this->session->set('msgerror','8');      // 'ERROR: Not allowed.'   
                return $this->redirectToRoute('message');     
            }
        } // if not updating a eshop post
        else{
            $data['eshop'] = "";
        }       
        return $this->render('eshop/eshop_template.html.twig', $data);      
    }



    /**
    * @Route("/eshop/post_action/{eshop_id?}", name="post_action")
    */
    public function post_action( $eshop_id = FALSE, Request $request, ValidatorInterface $validator)
    {
        $token = $request->request->get("token");
        if (!$this->isCsrfTokenValid('eshop_form', $token)) {
            return new Response("Operation not allowed", Response::HTTP_OK, ['content-type' => 'text/plain']);
        }
        $eshop_content = $request->request->get("eshop");
        $userid = $this->session->get('userid');
        if (!$eshop_id) {
            $response = $this->eshop_model->new_eshop($userid, $eshop_content );
            $this->session->set('msgerror','3');      // 'SUCCESS: New post submitted.'
            return $this->redirectToRoute('message');     
        }
        $eshop = $this->eshop_model->get_eshop($eshop_id);
        if ($userid == $eshop['user_id']) {
            $response = $this->eshop_model->update_eshop($eshop_id, $eshop_content); 
            $this->session->set('msgerror','9');         // 'SUCCESS: Post edited.'
            return $this->redirectToRoute('message');    
        }
        else {
            $this->session->set('msgerror','8');      // 'ERROR: Not allowed.'
            return $this->redirectToRoute('message');     
        }
    }


    /**
    * @Route("/eshop/checkout", name="checkout")
    **/
    public function checkout()
    { 
        return true;   
    }


    /**
    * @Route("/eshop/orders", name="orders")
    **/
    public function orders()
    { 
        return true;   
    }


    /**
    * @Route("/eshop/order", name="order")
    **/
    public function order(Request $request)
    { 
        return true;   
    }


    /**
    * @Route("/eshop/empty_cart", name="empty_cart") //TODO ??    
    **/   
    public function empty_cart()
    { 
        return true;   
    }

    /**
    * @Route("/eshop/message", name="message")
    */
    public function message()
    {      
        // Assign text message and set panel type
        switch($this->session->get('msgerror')) {
        //switch( $msgcode) {
            case  "1": // logged in msg
                $data['message'] = 'Welcome back '  . $this->session->get('name') .'!';
                $data['panel_contextual_class'] = 'panel-success';  
                break;
            case  "2":  // logout msg
                $data['message'] = 'See you back soon!';
                $data['panel_contextual_class'] = 'panel-info';
                break;
            case  "3": 
                $data['message'] = 'SUCCESS: New post submitted.';
                $data['panel_contextual_class'] = 'panel-success';
                break;  
            case  "4": 
                $data['message'] = 'ERROR: SQL INSERT error.';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "5": 
                $data['message'] = 'ERROR: SQL UPDATE error.';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "6": 
                $data['message'] = 'ERROR: Login first.';
                $data['panel_contextual_class'] = 'panel-warning';
                break;
            case  "7": 
                $data['message'] = 'ERROR: Please logout first.';
                $data['panel_contextual_class'] = 'panel-warning';
                break;
            case  "8": 
                $data['message'] = 'ERROR: Not allowed.';
                $data['panel_contextual_class'] = 'panel-warning';
                break;
            case  "9": 
                $data['message'] = 'SUCCESS: Post edited.';
                $data['panel_contextual_class'] = 'panel-success';
                break;
            case  "10": 
                $data['message'] = 'SUCCESS: Password reset activated! <br> Email sent to you :-)';
                $data['panel_contextual_class'] = 'panel-success';
                break;
            case  "11": 
                $data['message'] = 'Password reset successfully!';
                $data['panel_contextual_class'] = 'panel-warning';
                break;
            case  "12": 
                $data['message'] = 'ERROR: WRONG TOKEN OR TOKEN EXPIRED, PASSWORD RESET FAILED!';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "13": 
                $data['message'] = 'ERROR: Password reset failure! <br> Email not sent :-(';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "14": 
                $data['message'] = 'ERROR: SQL DB CONNECTION error! <br> Retrying connection.';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "15": 
                $data['message'] = 'ERROR: Unexpected origin. Please retry.';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
            case  "16": 
                $data['message'] = 'Registration successful. Welcome ' . $this->session->get('name') .'!';
                $data['panel_contextual_class'] = 'panel-danger';
                break;
        }  
        dump($this->session->get('msgerror'));
        $this->session->set('msgerror','');
        return $this->render('eshop/message.html.twig', $data);     
    }



    public function cookie_set($remember_digest)
    {
        // Assigning cookie data
        $response = new Response();
        $response->headers->setCookie(Cookie::create('siteAuth', $remember_digest, new \DateTime('now +30 day'), 'daw.deei.fct.ualg.pt/~a14038'));
        $this->eshop_model->set_remember_digest( $this->session->get('email'), $remember_digest);
        $response->send();         
    }



    public function cookie_reset()
    {
        // Assigning cookie data
        $response = new Response();
        $response->headers->setCookie(Cookie::create('siteAuth', '0', new \DateTime('now -1 day'), 'daw.deei.fct.ualg.pt/~a14038'));
        $this->eshop_model->set_remember_digest($this->session->get('email'), NULL);
        $response->send();         
    }



    private function requestValidation($input, $constraints)
    {      
        $violations = $this->validator->validate($input, $constraints);  
        $errorMessages = [];  
        $data['panel_contextual_class'] = 'panel-success';
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $data['panel_contextual_class'] = 'panel-warning';
        }   
        $data['errors'] = count($violations);
        $data['errorMessages'] = $errorMessages;            
        return $data;
    }
}