<?php

class RenderModel extends Model
{
    public function getUsername($id = 1) {
        $result = $this->db()->selectOne('select username from t_user where id = :id limit 1', array(':id' => $id));
        return $result['username'];
    }

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
        limit 2 offset :po
        ';
        return $this->db()->select($sql, [':id' => $id, ':po' => $pageOffset]);
    }
}