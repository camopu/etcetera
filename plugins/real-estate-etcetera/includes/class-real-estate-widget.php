<?php

/**
 * Класс для виджета фильтра объектов недвижимости
 *
 * @since      1.0.0
 * @package    Real_Estate
 * @subpackage Real_Estate/includes
 */
class Real_Estate_Widget extends WP_Widget {
    private $real_estate;

    /**
     * Конструктор класса
     */
    function __construct() {
        parent::__construct(
            'real_estate_widget',
            __('Фильтр объектов недвижимости', 'text_domain'),
            array('description' => __('Виджет для фильтрации объектов недвижимости'))
        );
        $this->real_estate = new Real_Estate();
    }

    /**
     * Вывод содержимого виджета
     *
     * @param array $args Аргументы виджета
     * @param array $instance Экземпляр виджета
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $this->real_estate->real_estate_shortcode();
        echo $args['after_widget'];
    }
}
