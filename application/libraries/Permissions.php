<?php

class Permissions
{
    private static $iredis = null;

    function __construct()
    {
    }

    public function connect()
    {
        if (!is_object(self::$iredis)) {
            self::$iredis = new Redis();
            self::$iredis->connect(REDIS_SERVER, REDIS_PORT, REDIS_TIME_OUT);
        }
    }

    public function setPermissions($admin_user_id, $permission_array)
    {
        if (empty($admin_user_id) || empty($permission_array)) {
            error_log('Set admin permission failed, empty parameters.');
            return false;
        }

        $this->connect();
        self::$iredis->del(PERMISSION_PREFIX . $admin_user_id);

        if (is_array($permission_array)) {
            foreach ($permission_array as $permission_item) {
                self::$iredis->lpush(PERMISSION_PREFIX . $admin_user_id, $permission_item);
            }
        } else {
            self::$iredis->lpush(PERMISSION_PREFIX . $admin_user_id, $permission_array);
        }

        self::$iredis->expire(PERMISSION_PREFIX . $admin_user_id, REDIS_TTL);
        return true;
    }

    public function getPermissions($admin_user_id)
    {
        if (empty($admin_user_id)) {
            error_log('Get admin permission failed, empty parameters.');
            return false;
        }

        $this->connect();
        return self::$iredis->lrange(PERMISSION_PREFIX . $admin_user_id, 0, -1);
    }

    public function checkPermission($admin_user_id, $authority_id)
    {
        if (empty($admin_user_id) || empty($authority_id)) {
            error_log('check admin permission failed, empty parameters.');
            return false;
        }

        $this->connect();
        $p_array = self::$iredis->lrange(PERMISSION_PREFIX . $admin_user_id, 0, -1);
        return in_array($authority_id, $p_array);
    }

    public function getAuthMenus($admin_user_id, $is_admin)
    {
        $display_menus = array();

        if ($is_admin) {
            return Authorityenum::get_display_list();
        }

        if (empty($admin_user_id)) {
            error_log('Get auth menu failed, empty parameters.');
            return false;
        }

        $auths = $this->getPermissions($admin_user_id);

        foreach (Authorityenum::get_display_list() as $menu_item) {
            if (in_array($menu_item['auth_code'], $auths)) {
                array_push($display_menus, $menu_item);
            }
        }

        return $display_menus;
    }
}