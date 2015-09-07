<?php

class MainController extends controllerManager
{
    public static $url = [
        'index' => [
            'url' => '/',
            'title' => 'Online store of goods'],
        'catalog' => [
            'patternUrl' => '~^/catalog/(\d+)$~m'
        ],
        'subCatalog' => [
            'patternUrl' => '~^/sub_catalog/(\d+)$~m'
        ],
        'goodsBunch' => [
            'patternUrl' => '~^/goods_bunch/(\d+)(?:/page/(\d+)){0,1}$~m'
        ]
    ];
    public function preController() {}

    public function index() {
        set(array(
            'name' => 'asas',
            'surname' => 'Zavalishin99',
            'age' => 5,
        ));
        set('var1', '11+++11');
    }

    /**
     * Catalog action
     */
    public function catalog() {
        $title = 'Catalog';
        $match = $this->getMatchUrl();
        if (empty($data = $this->model('render')->getCatalogById($match[1]))) {
            set('catalogNotFound', 1);
        } else {
            $subCatalogs = array_combine(
                array_unique(array_column($data, 'sc_id')),
                array_unique(array_column($data, 'sc_title'))
            );
            $subCatalogs = array_map(function ($element) {
                return ['title' => $element];
            }, $subCatalogs);

            foreach ($data as $value) {
                if (!empty($value['gb_title']) && $value['gb_enabled'] === true) {
                    $subCatalogs[ $value[ 'gb_sub_catalog_id' ] ]['goods_bunch'][ $value[ 'gb_id' ] ] = $value['gb_title'];
                }
            }
            set(['subCatalog' => $subCatalogs, 'catalogId' => $data[0]['gc_id']]);
            set('quickExplore', [
                0 => ['url' => '/catalog/'.$data[0]['gc_id'], 'title' => $data[0]['gc_title']]
            ]);
            $title = "Catalog of \"".$data[0]['gc_title']."\"";
        }
        $this->setTitle($title);
    }

    /**
     * Sub catalog action
     */
    public function subCatalog() {
        $title = 'Sub catalog';
        $match = $this->getMatchUrl();
        if (empty($data = $this->model('render')->getSubCatalogById($match[1]))) {
            set('subCatalogNotFound', 1);
        } else {
            $goodsBunch = [];
            foreach ($data as $element) {
                $goodsBunch[] = [
                    'id' => $element['gb_id'],
                    'title' => $element['gb_title']
                ];
            }
            set(['subCatalog' => $data[0]['sc_title'], 'catalogId' => $data[0]['gc_id'], 'goodsBunch' => $goodsBunch]);
            set('quickExplore', [
                0 => ['url' => '/catalog/'.$data[0]['gc_id'], 'title' => $data[0]['gc_title']],
                1 => ['url' => '/sub_catalog/'.$data[0]['sc_id'], 'title' => $data[0]['sc_title']]
            ]);
            $title .= " of \"".$data[0]['sc_title']."\"";
        }
        $this->setTitle($title);
    }

    /**
     * Goods bunch action
     */
    public function goodsBunch() {
        $title = 'Goods Bunch';
        $match = $this->getMatchUrl();

        /**
         * Determine catalog
         */
        if (empty($data = $this->model('render')->getProductsByGoodsBunchId( $match[1], isset($match[2])?($match[2]-1)*2:0 ))) {
            set('goodsBunchNotFound', 1);
        } else {
            /**
             * Calculation count of page
             */
            $productsCount = $data[0]['products_count'];
            $divided = $productsCount / 2;
            $pageCount = is_float($divided) ? intval(floor(++$divided)) : $divided;
            set('pagination', $this->getPaginationArray($match[2], $pageCount));
            set(['pageCount' => $pageCount, 'currentPage' => $match[2]]);

            $title = 'Goods bunch';
            $typeCatalog = [1 => 'books'];
            $catalogId = (integer)$data[0]['gc_id'];
            if (!array_key_exists($catalogId, $typeCatalog)) {
                crash('For this catalog was not created link to appropriate class');
            }
            $this->setView('mainList', $typeCatalog[$catalogId]);
            $typeProduct = call_user_func([$this, $typeCatalog[$catalogId].'Category'], $data);
            set(['catalogId' => $data[0]['gc_id'], 'goodsBunch' => $data[0]['gb_title'], 'typeProduct' => $typeProduct]);
            set('quickExplore', [
                0 => ['url' => '/catalog/'.$data[0]['gc_id'], 'title' => $data[0]['gc_title']],
                1 => ['url' => '/sub_catalog/'.$data[0]['sc_id'], 'title' => $data[0]['sc_title']],
                2 => ['url' => '/goods_bunch/'.$data[0]['gb_id'], 'title' => $data[0]['gb_title']]
            ]);
            $title .= ' of "'.$data[0]['gb_title'].'"';
        }
        $this->setTitle($title);
    }

    /**
     * Get pagination interval
     *
     * @param $page
     * @param $pageCount
     * @return array
     */
    private function getPaginationArray($page, $pageCount) {
        if ($page > $pageCount) {
            return ['isError' => true];
        }
        if ($pageCount <= 1) {
            return ['isPagination' => false];
        }
        $result = [
            'isPagination' => true,
            'isError' => false,
        ];
        if ($pageCount < 5) {
            return range($page, $pageCount);
        }
        if ($page == 1) {
            $interval = [1, $page+1, $pageCount];
        } elseif ($page == $pageCount) {
            $interval = [1, $pageCount-1, $pageCount];
        } else {
            $interval = array_unique([1, $page-1, $page, $page+1, $pageCount]);
        }
        return array_merge($result, ['interval' => $interval]);
    }

    /**
     * Get books category
     *
     * @param $data
     * @return array
     */
    private function booksCategory($data) {
        $return = [];
        $productsId = array_column($data, 'p_id');
        $keys = array_map(function ($element){
            return ++$element;
        }, array_keys($productsId));
        $result = array_combine($keys, $productsId);
        $sql = 'select id b_id, title b_title, product_id b_product_id from online_store.books where product_id in ('.implode(array_fill(1, count($keys), '?'), ', ').')';
        if (empty($books = $this->db()->select($sql, $result))) {
            crash('Query for select books return empty result');
        }
        if (count($books) !== count($data)) {
            crash('Books and products do not match by count');
        }
        $preparedBooks = array_combine(array_column($books, 'b_product_id'), $books);
        foreach ($data as $element) {
            $return[] = [
                'p_id' => $element['p_id'],
                'p_price' => $element['p_price'],
                'p_presence' => $element['p_presence'],
                'b_id' => $preparedBooks[ $element['p_id'] ]['b_id'],
                'b_title' => $preparedBooks[ $element['p_id'] ]['b_title'],
            ];
        }
        return $return;
    }
}