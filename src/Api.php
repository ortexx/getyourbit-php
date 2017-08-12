<?php
/**
 * Getyourbit.com client
 *
 * @package GetYourBit
 */

namespace GetYourBit;

use Requests;
use \Exception;

class Api {
    public function __construct($url) {
        $this->url = preg_replace('/[\/]+$/', '', $url);
        $this->token = null;
    }

    protected function _request($url, $options = array()) {
        $headers = array();
        $data = array();

        if(array_key_exists('headers', $options)) {
             $headers = $options['headers'];
             unset($options['headers']);
        }

        if(array_key_exists('data', $options)) {
             $data = $options['data'];
             unset($options['headers']);
        }

        $headers['Content-Type'] = 'application/json';
        $response = Requests::post($url, $headers, json_encode($data), $options);
        $body = json_decode($response->body, true);

        if(isset($body['error'])) {
            $message = $body['message'];

            if(isset($ody['meta'])) {
                $message = $message . ' ' . json_encode($body['meta']);
            }    

            throw new Exception($message);
        }

        return $body;
    }

    protected function next($res, $url, $options, $callback, $scroll) {
        if($scroll) {
            $options['data']['scroll'] = $scroll;
        }

        $body = $this->request($url, $options);
        $res = array_merge($res, $body['data']);

        if($callback) {
            $callback($body, $body['data'], $res);
        }

        if(isset($body['scroll'])) {
            return $this->next($res, $url, $options, $callback, $body['scroll']);
        }

        return $res;
    }
   
    public function auth($user, $password, $options = array()) {
        $options['auth'] = array($user, $password);
        $body = $this->_request($this->url . '/auth/', $options);
        $this->token = $body['token'];

        return $this->token;
    }

    public function logout($options = array()) {
        if(!$this->token) {
            throw new Exception('You have to login before to logout');
        }

        $options['data'] = array('token' => $this->token);
        $body = $this->_request($this->url . '/logout/', $options);
        $this->token = null;

        return $body;
    }

    public function request($url, $data = array(), $options = array()) {
        $data['token'] = $this->token;
        $options['data'] = $data;
        $url = $this->url . '/' . preg_replace('/^[\/]+/', '', $url);

        return $this->_request($url, $options);
    }

    public function scroll($url, $data = array(), $options = array(), $callback = null) {        
        if(is_callable($options)) {
            $callback = $options;
            $options = array();
        }

        if(is_callable($data)) {
            $callback = $data;
            $data = array();
            $options = array();
        }

        if(!count($options)) {
            $options = array();
        }

        $options['data'] = $data;
        $res = array();
        return $this->next($res, $url, $options, $callback, null);
    }
}
