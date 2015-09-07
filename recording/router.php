<?php function getRoute(){return array(
'/'=>array('title'=>'Online store of goods','function'=>'index','controller'=>'main'),
'~^/catalog/(\d+)$~m'=>array('function'=>'catalog','controller'=>'main'),
'~^/sub_catalog/(\d+)$~m'=>array('function'=>'subCatalog','controller'=>'main'),
'~^/goods_bunch/(\d+)(?:/page/(\d+)){0,1}$~m'=>array('function'=>'goodsBunch','controller'=>'main'));}