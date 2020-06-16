<?php

 namespace brain\Http;

 use MaxCDN;

 class StackpathAPI extends MaxCDN {
     public function __construct($alias, $key, $secret, $options=null) {
         $this->MaxCDNrws_url = 'https://api.stackpath.com/v1';
         parent::__construct($alias, $key, $secret, $options);
     }
 }