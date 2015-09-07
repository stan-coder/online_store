<?php

class BooksController extends controllerManager
{
    public static $url = [
        'book' => [
            'patternUrl' => '~^/book/(\d+)$~m'
        ]
    ];

    public function book() {
        $title = 'Book ';
        $match = $this->getMatchUrl();
        if (empty($data = $this->model('render')->getBookById($match[1]))) {
            $title .= 'Book ';
            set('bookNotFound', 1);
        } else {
            set([
                'title' => $data[0]['b_title'],
                'price' => $data[0]['p_price'],
                'product_id' => $data[0]['p_id'],
                'presenceText' => $data[0]['p_presence']==true?'На складе':'<span class="clGray">Нет в наличие</span>',
                'isPresence' => $data[0]['p_presence'],
                'condition' => $data[0]['p_new_mark']==true?'Новая':'Б/у',
                'likes' =>  $data[0]['count_likes'],
                'dislikes' =>  $data[0]['count_dislikes'],
                'authors' => array_map(function($element) {
                    return [$element['a_id'], $element['a_initials']];
                }, $data),
                'description' => $data[0]['b_description']
            ]);
            set('quickExplore', $this->model('render')->getQuickExploreArray($data, 4));
        }
        $this->setTitle($title);
    }
}