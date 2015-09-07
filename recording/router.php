<?php function getRoute(){return array(
'~^/book/(\d+)$~m'=>array('function'=>'book','controller'=>'books'),
'/'=>array('title'=>'Online store of goods','function'=>'index','controller'=>'main'),
'~^/catalog/(\d+)$~m'=>array('function'=>'catalog','controller'=>'main'),
'~^/sub_catalog/(\d+)$~m'=>array('function'=>'subCatalog','controller'=>'main'),
'~^/goods_bunch/(\d+)(?:/page/(\d+)){0,1}$~m'=>array('function'=>'goodsBunch','controller'=>'main'),
'/registration'=>array('title'=>'Registration new user','function'=>'registration','controller'=>'user'),
'/login'=>array('title'=>'Login in system','css'=>array('0'=>'login'),'function'=>'login','controller'=>'user'));}