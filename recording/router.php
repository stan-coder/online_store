<?php function getRoute(){return array(
'~^/book/(\d+)$~m'=>array('function'=>'book','controller'=>'books'),
'/entity/ajax/like'=>array('ajax'=>true,'function'=>'like','controller'=>'entity'),
'~^/group/\d{14}$~m'=>array('js'=>array('0'=>'jquery-1.11.3.min.js','1'=>'sha512.min.js','2'=>'/public/js/mvc/views/baseView.js','3'=>'/public/js/mvc/origin.js'),'function'=>'sheet','controller'=>'groups'),
'/groups/ajax/getUsers'=>array('ajax'=>true,'function'=>'getUsers','controller'=>'groups'),
'/groups/ajax/getInfo'=>array('ajax'=>true,'function'=>'getInfo','controller'=>'groups'),
'/'=>array('title'=>'Online store of goods','function'=>'index','controller'=>'main'),
'~^/catalog/(\d+)$~m'=>array('function'=>'catalog','controller'=>'main'),
'~^/sub_catalog/(\d+)$~m'=>array('function'=>'subCatalog','controller'=>'main'),
'~^/goods_bunch/(\d+)(?:/page/(\d+)){0,1}$~m'=>array('function'=>'goodsBunch','controller'=>'main'),
'/registration'=>array('title'=>'Registration new user','js'=>array('0'=>'sha512.min.js','1'=>'jquery-1.11.3.min.js','2'=>'encodePasswordRegistration.js'),'function'=>'registration','controller'=>'user'),
'/sign_in'=>array('title'=>'Login in system','css'=>array('0'=>'login.css'),'js'=>array('0'=>'sha512.min.js','1'=>'jquery-1.11.3.min.js','2'=>'encodePasswordSignIn.js'),'function'=>'signIn','controller'=>'user'),
'/sign_out'=>array('function'=>'signOut','controller'=>'user'),
'/profile'=>array('function'=>'profile','controller'=>'user'));}