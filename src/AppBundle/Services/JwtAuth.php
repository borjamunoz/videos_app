<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth {
   
    public $manager;
    
    public function __construct($manager) {
        $this->manager = $manager;
    }
    
    public function signUp($email, $password, $getHash = null) {
        $key = "secret-key";
        
        $user = $this->manager->getRepository("BackendBundle:User")->findOneBy(
                    array(
                        "email" => $email,
                        "password" => $password
                    )
                );
        $signUp = false;
        if(is_object($user)){
            $signUp = true;
        }
        
        if($signUp == true) {
            $token = array(
                "sub" => $user->getId(),
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "password" => $user->getPassword(),
                "image" => $user->getImage(),
                "iat" => time(),
                "exp" => time() + (7*24*60*60)
            );
            $jwt = JWT::encode($token, $key, 'HS256');
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            
            if($getHash != null){
                return $jwt;
            } else {
                return $decoded;
            }
            
        } else {
            return array('status' => 'error', 'data' => 'Login error');
        }
    }
}
