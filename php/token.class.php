<?php
require_once("vendor/autoload.php");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\WrongKeyException;

class Token{
    private $k="";
    public $jwt;
    function __construct($k=false) {
        if($k){
            $this->k=$k;
        }
    }
    public function create($payload){
        $this->jwt = JWT::encode($payload, $this->k, 'HS256');
    }
    public function decode($token) {
        //JWT::$leeway = 60; // $leeway 单位：秒
        try {
            $decoded = JWT::decode($token, new Key($this->k,'HS256'));
            return array('success' => true, 'payload' => $decoded);
        } catch (ExpiredException $e) {
            return array('success' => false, 'error' => 'Token过期');
        } catch (BeforeValidException $e) {
            return array('success' => false, 'error' => 'Token在有效时间之前使用');
        } catch (SignatureInvalidException $e) {
            return array('success' => false, 'error' => 'Token签名无效');
        } catch (WrongKeyException $e) {
            return array('success' => false, 'error' => '使用了错误的密钥');
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}