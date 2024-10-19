<?php

/**
 * Класс для изменения запроса кастомного пост-типа
 *
 * Этот класс добавляет сортировку по полю "экологичность" для кастомного пост-типа "real_estate".
 *
 * @since      1.0.0
 * @package    Real_Estate
 * @subpackage Real_Estate/includes
 */
class Real_Estate_Query {
    /**
     * Конструктор класса
     */
    public function __construct() {
        add_action('pre_get_posts', array($this, 'modify_query'));
    }

    /**
     * Изменяет запрос для кастомного пост-типа
     *
     * @param WP_Query $query Запрос WordPress
     */
    public function modify_query($query) {
        // Проверяем, что это не административный запрос и это архив кастомного пост-типа
        if (!is_admin() && $query->is_post_type_archive('real_estate')) {
            // Устанавливаем сортировку по полю "экологичность"
            $query->set('meta_key', 'ecological_rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
        }
    }
}