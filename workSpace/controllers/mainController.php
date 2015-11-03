<?php

class MainController extends controllerManager
{
    public static $paginationCountElement = 3;

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


        require_once(CORE . 'pdo2.php');
        db2::executeWithinTransaction(function(){
            db2::exec('insert into temp (`temp`) value (?)', 'string-111');
            db2::exec('insert into temp (`temz`) value (?)', 'string-333');
        });
        /*$res = db2::exec('select * from users where id <> ? limit 1', 100);
        //$res = db2::exec('insert into temp (`temp`) value (?)', 'string-111');
        echo '<pre>';
        print_r($res);
        echo '</pre>';*/

        exit;
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
            set('quickExplore', $this->model('render')->getQuickExploreArray($data, 1));
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
            set('quickExplore', $this->model('render')->getQuickExploreArray($data, 2));
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
        if (empty($data = $this->model('render')->getProductsByGoodsBunchId( $match[1], isset($match[2])?($match[2]-1)*self::$paginationCountElement:0 ))) {
            set('goodsBunchNotFound', 1);
        } else {
            /**
             * Calculation count of page
             */
            $productsCount = $data[0]['products_count'];
            $divided = $productsCount / self::$paginationCountElement;
            $pageCount = is_float($divided) ? intval(floor(++$divided)) : $divided;
            $currentPage = !isset($match[2])?1:$match[2];
            set('pagination', $this->getPaginationArray($currentPage, $pageCount));
            set(['pageCount' => $pageCount, 'currentPage' => $currentPage]);

            $title = 'Goods bunch';
            $typeCatalog = [1 => 'books'];
            $catalogId = (integer)$data[0]['gc_id'];
            if (!array_key_exists($catalogId, $typeCatalog)) {
                crash('For this catalog was not created link to appropriate class');
            }
            $this->setView('mainList', $typeCatalog[$catalogId]);
            $typeProduct = call_user_func([$this, $typeCatalog[$catalogId].'Category'], $data);
            set(['catalogId' => $data[0]['gc_id'], 'goodsBunch' => $data[0]['gb_title'], 'typeProduct' => $typeProduct]);
            set('quickExplore', $this->model('render')->getQuickExploreArray($data, 3));
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
            return ['isPagination' => false, 'isError' => false];
        }
        $result = ['isPagination' => true,'isError' => false];
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
        if (empty( $books = $this->model('render')->getBooksByProductsListId(implode(array_fill(1, count($keys), '?'), ', '), array_combine($keys, $productsId)) )) {
            crash('Query for select books return empty result');
        }

        $booksId = array_unique(array_column($books, 'b_id'));
        if (count($booksId) !== count($data)) {
            crash('Books and products do not match by count');
        }
        $preparedBooks = [];
        /**
         * Fill array with authors
         */
        foreach ($books as $book) {
            if (!isset($preparedBooks[$book['b_product_id']])) {
                $preparedBooks[ $book['b_product_id'] ] = [
                    'b_title' => $book['b_title'],
                    'b_id' => $book['b_id'],
                ];
            }
            if (is_numeric($book['a_id'])) {
                $preparedBooks[ $book['b_product_id'] ]['authors'][ $book['a_id'] ] = [
                    'id' => $book['a_id'],
                    'initials' => $book['a_first_name'] . ' ' . $book['a_surname'],
                ];
            }
        }
        foreach ($data as $element) {
            $return[] = [
                'p_id' => $element['p_id'],
                'p_price' => $element['p_price'],
                'p_presence' => $element['p_presence'],
                'b_id' => $preparedBooks[ $element['p_id'] ]['b_id'],
                'b_title' => $preparedBooks[ $element['p_id'] ]['b_title'],
                'authors' => isset($preparedBooks[ $element['p_id'] ]['authors']) ? $preparedBooks[ $element['p_id'] ]['authors'] : []
            ];
        }
        return $return;
    }
}