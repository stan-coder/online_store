<?php function getRoute(){return array(
'~^/book/(\d+)$~m'=>array('function'=>'book','controller'=>'books'),
'/'=>array('title'=>'Online store of goods','function'=>'index','controller'=>'main'),
'~^/catalog/(\d+)$~m'=>array('function'=>'catalog','controller'=>'main'),
'~^/sub_catalog/(\d+)$~m'=>array('function'=>'subCatalog','controller'=>'main'),
'~^/goods_bunch/(\d+)(?:/page/(\d+)){0,1}$~m'=>array('function'=>'goodsBunch','controller'=>'main'),
'/registration'=>array('title'=>'Registration new user','js'=>array('0'=>'sha512.min.js','1'=>'jquery-1.11.3.min.js','2'=>'encodePassword.js'),'function'=>'registration','controller'=>'user'),
'/sign_in'=>array('title'=>'Login in system','css'=>array('0'=>'login'),'function'=>'signIn','controller'=>'user'));}