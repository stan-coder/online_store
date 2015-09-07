<?php

class RenderModel extends Model
{
    public function getCatalogById($id) {
        $sql = '
        select
            sc.id sc_id, sc.title sc_title, sc.enabled sc_enabled, sc.catalog_id sc_catalog_id,
            gb.id gb_id, gb.title gb_title, gb.enabled gb_enabled, gb.sub_catalog_id gb_sub_catalog_id,
            gc.id gc_id, gc.title gc_title
        from online_store.general_catalog gc
        left join online_store.sub_catalog sc on gc.id = sc.catalog_id
        left join online_store.goods_bunch gb on sc.id = gb.sub_catalog_id

        where gc.id = ? and gc.enabled = true and sc.enabled = true
        order by sc.ordering asc';

        return $this->db()->select($sql, [$id]);
    }

    public function getSubCatalogById($id) {
        $sql = '
        select
            sc.id sc_id, sc.title sc_title,
            gb.id gb_id, gb.title gb_title,
            gc.id gc_id, gc.title gc_title
        from online_store.sub_catalog sc
        left join online_store.goods_bunch gb on sc.id = gb.sub_catalog_id
        left join online_store.general_catalog gc on sc.catalog_id = gc.id

        where sc.id = ? and sc.enabled = true and gb.enabled = true and gc.enabled = true
        ';
        return $this->db()->select($sql, [$id]);
    }

    public function getProductsByGoodsBunchId($id, $pageOffset = 0) {
        $sql = '
        select
            p.id p_id, p.price p_price, p.presence p_presence, p.new_mark p_new_mark,
            gb.id gb_id, gb.title gb_title,
            sc.id sc_id, sc.title sc_title,
            gc.id gc_id, gc.title gc_title,
            (select count(id) from online_store.products as inp
            where inp.goods_bunch_id = :id and gb.enabled = true and sc.enabled = true and gc.enabled = true) as products_count
        from online_store.products p
        left join online_store.goods_bunch gb on p.goods_bunch_id = gb.id
        left join online_store.sub_catalog sc on gb.sub_catalog_id = sc.id
        left join online_store.general_catalog gc on sc.catalog_id = gc.id

        where p.goods_bunch_id = :id and gb.enabled = true and sc.enabled = true and gc.enabled = true
        order by p.id asc
        limit '.MainController::$paginationCountElement.' offset :po
        ';
        return $this->db()->select($sql, [':id' => $id, ':po' => $pageOffset]);
    }

    public function getBooksByProductsListId($placeholders, $data) {
        $sql = '
        select
            b.id b_id, b.title b_title, b.product_id b_product_id,
            a.id a_id, array_to_string(a.initials, \' \') as a_initials
            from online_store.books b
        left join online_store.authors_books ab on ab.book_id = b.id
        left join online_store.authors a on ab.author_id = a.id

        where product_id in ('.$placeholders.')';
        return $this->db()->select($sql, $data);
    }

    public function getBookById($id) {
        $sql = '
        select
            b.id b_id, b.title b_title, b.description b_description,
            p.id p_id, p.price p_price, p.presence p_presence, p.new_mark p_new_mark,
            a.id a_id, array_to_string(a.initials, \' \') as a_initials,
            gb.id gb_id, gb.title gb_title,
            sc.id sc_id, sc.title sc_title,
            gc.id gc_id, gc.title gc_title,
            (select count(id) from online_store.likes l1 where l1.assessment = true and l1.product_id = p.id) as count_likes,
            (select count(id) from online_store.likes l2 where l2.assessment = false and l2.product_id = p.id) as count_dislikes

        from online_store.books b
        left join online_store.products p on b.product_id = p.id
        left join online_store.authors_books ab on b.id = ab.book_id
        left join online_store.authors a on ab.author_id = a.id
        left join online_store.goods_bunch gb on p.goods_bunch_id = gb.id
        left join online_store.sub_catalog sc on gb.sub_catalog_id = sc.id
        left join online_store.general_catalog gc on sc.catalog_id = gc.id

        where b.id = ? and gb.enabled = true and sc.enabled = true and gc.enabled = true
        ';
        return $this->db()->select($sql, [$id]);
    }

    public function getQuickExploreArray($data, $count) {
        $parameters = [
            ['catalog', 'gc'],
            ['sub_catalog', 'sc'],
            ['goods_bunch', 'gb'],
            ['book', 'b']
        ];
        $result = range(0, $count-1);
        return array_map(function ($element) use ($data, $parameters) {
            return [
                'url' => '/'.$parameters[$element][0].'/'.$data[0][$parameters[$element][1].'_id'],
                'title' => $data[0][$parameters[$element][1].'_title']];
        }, $result);
    }
}