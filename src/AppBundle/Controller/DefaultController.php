<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    public function loginAction(Request $request) 
    {
        $helpers = $this->get("app.helpers");
        $jwtAuth = $this->get("app.jwt_auth");
        $json = $request->get('json', null);
        
        if($json != null) {
            $params = json_decode($json);
            
            $email = (isset($params->email) ? $params->email : null);
            $password = (isset($params->password) ? $params->password : null);
            $getHash = (isset($params->getHash) ? $params->getHash : null);

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "email not valid";
            $validateEmail = $this->get("validator")->validate($email, $emailConstraint);
            
            if(count($validateEmail) == 0 && ($password != null)) {
                if($getHash != null) {
                    $signUp = $jwtAuth->signUp($email, $password, true);
                } else {
                    $signUp = $jwtAuth->signUp($email, $password);
                }
                return new JsonResponse($signUp);
            } else {
                return $helpers->json(
                            array(
                                'status' => 'error',
                                'data' => 'Data error'
                            )
                        );
            }
        }
        return $signUp;
    }

    public function getUsersAction()
    {
        $helpers = $this->get("app.helpers");
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BackendBundle:User')->findAll();
        
        return $helpers->json($users);
    }
}
