<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tbrateenum
{
    private static $TB_RATE_LIST = array(
        ['display' => false, 'id' => '1', 'rate_name' => '1心'],
        ['display' => false, 'id' => '2', 'rate_name' => '2心'],
        ['display' => true, 'id' => '3', 'rate_name' => '3心'],
        ['display' => true, 'id' => '4', 'rate_name' => '4心'],
        ['display' => true, 'id' => '5', 'rate_name' => '5心'],
        ['display' => true, 'id' => '6', 'rate_name' => '1钻'],
        ['display' => true, 'id' => '7', 'rate_name' => '2钻'],
        ['display' => true, 'id' => '8', 'rate_name' => '3钻'],
        ['display' => true, 'id' => '9', 'rate_name' => '4钻'],
        ['display' => true, 'id' => '10', 'rate_name' => '5钻'],
        ['display' => true, 'id' => '11', 'rate_name' => '1皇冠'],
        ['display' => true, 'id' => '12', 'rate_name' => '2皇冠'],
        ['display' => true, 'id' => '13', 'rate_name' => '3皇冠'],
        ['display' => true, 'id' => '14', 'rate_name' => '4皇冠'],
        ['display' => true, 'id' => '15', 'rate_name' => '5皇冠']
    );

    function __construct()
    {
    }

    public static function _get_all()
    {
        return self::$TB_RATE_LIST;
    }

    public static function _get_name($i)
    {
        foreach (self::$TB_RATE_LIST as $menu_item) {
            if ($menu_item['id'] == $i) {
                return $menu_item['rate_name'];
            }
        }

        return null;
    }

    public static function get_display_list()
    {
        $displayed = array();

        foreach (self::$TB_RATE_LIST as $menu_item) {
            if ($menu_item['display']) {
                array_push($displayed, $menu_item);
            }
        }

        return $displayed;
    }
}