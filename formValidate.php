<?php

class formValidate{
    private $data;
    private $errors=[];
    private static $fields = ['Username','Age','Email','Password'];

    public function __construct($postData) {
        $this->data = $postData;
    }

    public function validateForm(){
        foreach(self::$fields as $field){
                if(!array_key_exists($field , $this->data)){
                trigger_error("$field is not in data");
                return;
                }
        }
        $this->validateUserName();
        $this->validateAge();
        $this->validateEmail();
        $this->validatePassword();
        return $this->errors;
    }
    private function validateUserName(){
        $name=trim($this->data['Username']) ?? null;
        $name=htmlspecialchars($name);
        if(empty($name)){
            $this->addError('Username','username cannot be empty');
        }else{
            if(!preg_match('/^[a-zA-Z0-9 ]{1,15}$/',$name)){
                $this->addError('Username','username must be 1-15 chars & alphanumeric');
            }
        }

    }
    private function validateAge(){
        $age=$this->data['Age'] ?? null;
        if(!is_numeric($age)){
            $this->addError('Age','Age cannot be empty');
        }else{
            $age=(int)$age;
            if($age<10||$age>100){
                $this->addError('Age','Age must be 10-100');
            }
        }

    }
    private function validateEmail(){
        $email=trim($this->data['Email']) ?? null;
        if(empty($email)){
            $this->addError('Email','Email cannot be empty');
        }else{
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $this->addError('Email','Email must be valid');
            }
        }
    }
    private function validatePassword(){
        $password=trim($this->data['Password']) ?? null;
        if(empty($password)){
            $this->addError('Password','Password cannot be empty');
        }
        else if(strlen($password)<6){
            $this->addError('Password','Password must be at least 6 chars');
        }else{
            if(!preg_match('/[A-Z]/',$password)){
                $this->addError('Password','Password must contain at least 1 upper char');
            }
            if(!preg_match('/[^a-zA-Z0-9]/',$password)){
                $this->addError('Password','Password must contain at least 1 special char');
            }
        
        }

        }
    private function addError($key,$value){
        $this->errors[$key]=$value;
    }

}

?>