<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hiltoncore extends Hilton_Model
{
    function __construct()
    {
        parent::__construct();
    }

    // -- manage user part --

    function manage_login_verify($UName, $UPwd)
    {

        $data = array();

        if (empty($UName) || empty($UPwd)) {
            $data['result'] = false;
            return $data;
        }

        $this->db->where('user_name', $UName);
        $this->db->where('passwd', $UPwd);
        $this->db->limit(1);
        $query = $this->db->get(self::DB_ADMIN_MEMBER);
        if ($query->num_rows() > 0) {
            $data['result'] = true;
            $data['status'] = $query->row()->status;
            $data['role'] = $query->row()->account_role;
            $data['realname'] = $query->row()->real_name;
            $data['id'] = $query->row()->id;
            return $data;
        }

        $data['result'] = false;
        return $data;
    }

    function change_admin_passwd($admin_user_id, $old_pwd, $new_pwd)
    {
        if (empty($admin_user_id) || empty($old_pwd) || empty($new_pwd)) {
            return CODE_BAD_PARAMETER;
        }

        $this->db->where('id', $admin_user_id);
        $this->db->where('passwd', $old_pwd);
        $this->db->limit(1);
        $query = $this->db->get(self::DB_ADMIN_MEMBER);
        if ($query->num_rows() > 0) {
            $data = array('passwd' => $new_pwd);
            $this->db->where('id', $admin_user_id);
            if ($this->db->update(self::DB_ADMIN_MEMBER, $data) > 0) {
                return CODE_SUCCESS;
            }
            return CODE_DB_ERROR;
        } else {
            return CODE_BAD_PASSWORD;
        }
    }

    function get_admin_list()
    {
        $this->db->order_by('status', 'desc');
        return $this->db->get(self::DB_ADMIN_MEMBER)->result();
    }

    function disable_admin_account($admin_user_id)
    {
        if (empty($admin_user_id) || !is_numeric($admin_user_id)) {
            return false;
        }

        $this->db->set('status', STATUS_DISABLE);
        $this->db->where('id', $admin_user_id);
        return $this->db->update(self::DB_ADMIN_MEMBER);
    }

    function reset_admin_passwd($admin_user_id)
    {
        if (empty($admin_user_id) || !is_numeric($admin_user_id)) {
            return false;
        }

        $this->db->set('passwd', '11016f87580f41dec47d132d00d4f580a24520cc');
        $this->db->where('id', $admin_user_id);
        return $this->db->update(self::DB_ADMIN_MEMBER);
    }

    function add_admin_account($user_name, $real_name, $role)
    {
        if (empty($user_name) || empty($real_name) || empty($role)) {
            return false;
        }

        $this->db->where('user_name', $user_name)->limit(1);
        $query = $this->db->get(self::DB_ADMIN_MEMBER);
        if ($query->num_rows() > 0) return false;

        $data = array(
            'user_name' => $user_name,
            'real_name' => $real_name,
            'passwd' => '11016f87580f41dec47d132d00d4f580a24520cc',
            'account_role' => $role
        );
        return $this->db->insert(self::DB_ADMIN_MEMBER, $data);
    }

    // -- manage permissions part --
    function get_manage_permissions($admin_id)
    {

        if (empty($admin_id)) {
            error_log('Get admin permission database failed, empty parameters.');
            return false;
        }

        $permission_array = array();

        $this->db->where('manage_id', $admin_id);
        $data = $this->db->get(self::DB_ADMIN_PERMISSION);
        if ($data->num_rows() > 0) {
            foreach ($data->result() as $item) {
                array_push($permission_array, $item->authority_id);
            }
        }
        return $permission_array;
    }

    function set_account_permissions($admin_id, $perm_array)
    {

        if (empty($admin_id) || !is_array($perm_array)) {
            return false;
        }

        // clean old permissions
        $this->db->where('manage_id', $admin_id)->delete(self::DB_ADMIN_PERMISSION);

        $insert_perm_data = array();
        foreach ($perm_array as $perm_item) {
            array_push($insert_perm_data, array('manage_id' => $admin_id, 'authority_id' => $perm_item));
        }

        return $this->db->insert_batch(self::DB_ADMIN_PERMISSION, $insert_perm_data);
    }

    // -- notice manage part --
    function get_notice_list($parameters)
    {
        if (!empty($parameters['keywords'])) {
            $this->db->like('title', $parameters['keywords']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->where('expire_time >=', date("Y-m-d H:i:s"));
        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_PLATFORM_NOTICE)->result();
    }

    function delete_notice($notice_id)
    {
        if (empty($notice_id) || !is_numeric($notice_id)) {
            return false;
        }

        $this->db->where('id', $notice_id);
        return $this->db->delete(self::DB_PLATFORM_NOTICE);
    }

    function get_notice_info($notice_id)
    {
        if (empty($notice_id) || !is_numeric($notice_id)) {
            return false;
        }

        $this->db->where('id', $notice_id);
        $this->db->limit(1);
        return $this->db->get(self::DB_PLATFORM_NOTICE)->row();
    }

    function add_new_notice($parameters)
    {
        if (empty($parameters['title']) || empty($parameters['expire_time'])) {
            return false;
        }

        $data = array(
            'oper_id' => $parameters['oper_id'],
            'notice_type' => $parameters['notice_type'],
            'title' => $parameters['title'],
            'content' => $parameters['content'],
            'sort' => $parameters['sort'] ? $parameters['sort']:1,
            'expire_time' => $parameters['expire_time']
        );

        return $this->db->insert(self::DB_PLATFORM_NOTICE, $data);
    }

    function update_notice_info($parameters)
    {
        if (empty($parameters['title']) || empty($parameters['expire_time']) || empty($parameters['notice_id'])) {
            return false;
        }

        $data = array(
            'oper_id' => $parameters['oper_id'],
            'notice_type' => $parameters['notice_type'],
            'title' => $parameters['title'],
            'content' => $parameters['content'],
            'sort' => $parameters['sort'] ? $parameters['sort']:1,
            'expire_time' => $parameters['expire_time']
        );

        $this->db->where('id', $parameters['notice_id']);

        return $this->db->update(self::DB_PLATFORM_NOTICE, $data);
    }

    // -- member manage part --
    function get_member_list($parameters)

    {
        if (empty($parameters['user_type'])) {
            return false;
        }

        $this->db->where('user_type', $parameters['user_type']);

        if (!empty($parameters['member_id'])) {
            $this->db->where('id', decode_id($parameters['member_id']));
            return $this->db->get(self::DB_USER_MEMBER)->result();
        }

        if (!empty($parameters['user_name'])) {
            $this->db->where('user_name', $parameters['user_name']);
            return $this->db->get(self::DB_USER_MEMBER)->result();
        }

        if (!empty($parameters['regDate'])) {
            $this->db->where('substr(reg_time,1,10)', $parameters['regDate']);
        }

        if (isset($parameters['auth_status']) && $parameters['auth_status'] != '') {
            $this->db->where('auth_status', $parameters['auth_status']);
        }

        if (isset($parameters['status']) && $parameters['status'] != '') {
            $this->db->where('status', $parameters['status']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_USER_MEMBER)->result();
    }

    function get_member_info($memberId, $aField = [])
    {
        $sField = empty($aField) ? '*' : implode(',', $aField);
        $this->db->select($sField);
        $this->db->where('id', $memberId);
        return $this->db->get(self::DB_USER_MEMBER)->row();
    }

    function get_member_exist($parameters)
    {
        if (empty($parameters['user_type']) || empty($parameters['member_id'])) {
            return 0;
        }

        $this->db->where('user_type', $parameters['user_type']);
        $this->db->where('id', decode_id($parameters['member_id']));

        return $this->db->get(self::DB_USER_MEMBER)->num_rows();
    }

    function set_account_ban($member_id)
    {
        if (empty($member_id)) {
            return false;
        }

        $this->db->set('status', STATUS_DISABLE);
        $this->db->where('id', $member_id);
        return $this->db->update(self::DB_USER_MEMBER);
    }

    function unset_account_ban($member_id)
    {
        if (empty($member_id)) {
            return false;
        }

        $this->db->set('status', STATUS_ENABLE);
        $this->db->where('id', $member_id);
        return $this->db->update(self::DB_USER_MEMBER);
    }

    function freezing_account_balance($member_id, $freezing_capital_amount, $freezing_commission_amount)
    {
        if (empty($member_id)) {
            return false;
        }

        if (!is_numeric($freezing_capital_amount) && !is_numeric($freezing_commission_amount)) {
            return false;
        }

        $this->db->set('freezing_capital_amount',       $freezing_capital_amount);
        $this->db->set('freezing_commission_amount',    $freezing_commission_amount);
        $this->db->where('id', $member_id);
        return $this->db->update(self::DB_USER_MEMBER);
    }

    function set_commission_discount($member_id, $commission_discount)
    {
        if (empty($member_id) || !is_numeric($commission_discount)) {
            return false;
        }

        $this->db->set('commission_discount', $commission_discount);
        $this->db->where('id', $member_id);
        return $this->db->update(self::DB_USER_MEMBER);
    }

    // -- Certification manage part --
    function get_all_cert_infos($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('user_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->where('status', STATUS_CHECKING);
        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_USER_CERT)->result();
    }

    /**
     * @param array $aParam
     * @return array
     */
    function getMemberInfo($aParam)
    {
        if (!empty($aParam['member_id'])) {
            $this->db->where('user_id', decode_id($aParam['member_id']));
        }

        //echo decode_id($aParam['member_id']);exit;

        if (!empty($aParam['status'])) {
            $this->db->where('status', intval($aParam['status']));
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_USER_CERT)->result();
    }

    function update_member_cert_infos($parameters){
        if (empty($parameters['member_id'])) { throw new Exception('会员ID缺失', CODE_BAD_REQUEST); }
        if (!empty($parameters['true_name'])) { $this->db->set('true_name', $parameters['true_name']); }
        if (!empty($parameters['id_card_num'])) { $this->db->set('id_card_num', $parameters['id_card_num']); }
        if (!empty($parameters['qq_num'])) { $this->db->set('qq_num', $parameters['qq_num']); }
        if (!empty($parameters['bank_card_num'])) { $this->db->set('bank_card_num', $parameters['bank_card_num']); }
        if (!empty($parameters['bank_name'])) { $this->db->set('bank_name', $parameters['bank_name']); }
        if (!empty($parameters['bank_province'])) { $this->db->set('bank_province', $parameters['bank_province']); }
        if (!empty($parameters['bank_city'])) { $this->db->set('bank_city', $parameters['bank_city']); }
        if (!empty($parameters['bank_county'])) { $this->db->set('bank_county', $parameters['bank_county']); }
        if (!empty($parameters['bank_branch'])) { $this->db->set('bank_branch', $parameters['bank_branch']); }

        $this->db->where('id', $parameters['member_id']);
        if (!$this->db->update(self::DB_USER_CERT)) { throw new Exception('信息修改失败', CODE_BAD_REQUEST);}
    }

    function approve_certification_info($cert_info_id, $oper_id)
    {
        if (empty($cert_info_id) || !is_numeric($cert_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->trans_start();

        $cert_info = $this->db->where('id', $cert_info_id)->get(self::DB_USER_CERT)->row();

        if (empty($cert_info)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $cert_info_id);
        if (!$this->db->update(self::DB_USER_CERT)) {
            return false;
        }

        $this->db->reset_query();
        $this->db->set('auth_status', STATUS_PASSED);
        $this->db->where('id', $cert_info->user_id);
        if (!$this->db->update(self::DB_USER_MEMBER)) {
            return false;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("update cert status failed, last query : " . $this->db->last_query());
            return false;
        }

        return true;
    }

    function reject_certification_info($cert_info_id, $oper_id, $assignStatus = '')
    {
        if (empty($cert_info_id) || !is_numeric($cert_info_id) || empty($oper_id)) {
            return false;
        }
        $this->db->trans_start();
        $cert_info = $this->db->where('id', $cert_info_id)->get(self::DB_USER_CERT)->row();
        if (empty($cert_info)) {
            return false;
        }

        // 是否修改成指定状态
        $status = !empty($assignStatus) ? $assignStatus : STATUS_FAILED;

        $this->db->set('status', $status);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $cert_info_id);
        if(!$this->db->update(self::DB_USER_CERT)){
            return false;
        }

        $this->db->reset_query();
        $this->db->set('auth_status', $status);
        $this->db->where('id', $cert_info->user_id);
        if (!$this->db->update(self::DB_USER_MEMBER)) {
            return false;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("update cert status failed, last query : " . $this->db->last_query());
            return false;
        }

        return true;
    }

    // -- shop manage part --
    function get_all_shop_infos($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('seller_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['shop_name'])) {
            $this->db->where('shop_name', $parameters['shop_name']);
        }

        if (!empty($parameters['shop_ww'])) {
            $this->db->where('shop_ww', $parameters['shop_ww']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['status'])) {
            $this->db->where('status', $parameters['status']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_SHOP_BIND)->result();
    }

    function get_shop($data)
    {
        if (!empty($data['id'])) {
            $this->db->where('id', $data['id']);
        }
        return $this->db->get(self::DB_SHOP_BIND)->result();
    }

    function updateShop($data)
    {
        if(empty($data)){
            return false;
        }

        $this->db->where('id', $data['id']);

        if(!empty($data['shop_name'])){
            $this->db->set('shop_name', $data['shop_name']);
        }

        return $this->db->update(self::DB_SHOP_BIND);
    }



    function get_status_check_shop_infos($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('seller_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->where('status', STATUS_CHECKING);
        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_SHOP_BIND)->result();
    }

    function approve_shop_info($shop_info_id, $oper_id)
    {
        if (empty($shop_info_id) || !is_numeric($shop_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $shop_info_id);
        return $this->db->update(self::DB_SHOP_BIND);
    }

    function reject_shop_info($shop_info_id, $oper_id)
    {
        if (empty($shop_info_id) || !is_numeric($shop_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_FAILED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $shop_info_id);
        return $this->db->update(self::DB_SHOP_BIND);
    }

    function set_shop_ban($shop_info_id)
    {
        if (empty($shop_info_id)) {
            return false;
        }

        $this->db->set('status', STATUS_BAN);
        $this->db->where('id', $shop_info_id);
        return $this->db->update(self::DB_SHOP_BIND);
    }

    function unset_shop_ban($shop_info_id)
    {
        if (empty($shop_info_id)) {
            return false;
        }

        $this->db->set('status', STATUS_FAILED);
        $this->db->where('id', $shop_info_id);
        return $this->db->update(self::DB_SHOP_BIND);
    }

    // -- taobao manage part --
    function get_all_taobao_infos($parameters)
    {
        $sql = 'SELECT a.*, b.user_name from user_bind_info a, user_members b where  a.user_id = b.id';
        $params = [];

        if (!empty($parameters['member_id'])) {
            $sql .= " and a.user_id = ? ";
            array_push($params, decode_id($parameters['member_id']));
        }

        if (!empty($parameters['user_name'])) {
            $sql .= " and b.user_name = ? ";
            array_push($params, $parameters['user_name']);
        }

        if (!empty($parameters['tb_nick'])) {
            $sql .= " and a.tb_nick = ? ";
            array_push($params, $parameters['tb_nick']);
        }

        if (!empty($parameters['start_time'])) {
            $sql .= " and a.gmt_create >= ? ";
            array_push($params, $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $sql .= " and a.gmt_create <= ? ";
            array_push($params, $parameters['end_time']);
        }

        if (!empty($parameters['status'])) {
            $sql .= " and a.status = ? ";
            array_push($params, $parameters['status']);
        }
        
        if (!empty($parameters['account_type'])) {
            $sql .= " and a.account_type = ? ";
            array_push($params, $parameters['account_type']);
        }

        $limit = limit_page($parameters['i_page'], ITEMS_PER_LOAD);
        $sql .= ' ORDER BY a.id desc';
        $sql .= " $limit";
        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    function get_status_check_taobao_infos($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('user_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['account_type'])) {
            $this->db->where('account_type', $parameters['account_type']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->where('status', STATUS_CHECKING);
        //$this->db->order_by('id', 'DESC');
        $this->db->order_by('gmt_update', 'DESC');
        return $this->db->get(self::DB_USER_BIND)->result();
    }

    function approve_taobao_info($taobao_info_id, $oper_id)
    {
        if (empty($taobao_info_id) || !is_numeric($taobao_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->set('tb_passed_time', date('Y-m-d H:i:s',time()));
        $this->db->where('id', $taobao_info_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    function reject_taobao_info($taobao_info_id, $oper_id)
    {
        if (empty($taobao_info_id) || !is_numeric($taobao_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_FAILED);
        $this->db->set('oper_id', $oper_id);
        $this->db->set('gmt_update', date('Y-m-d H:i:s',time()));
        $this->db->where('id', $taobao_info_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    function set_taobao_ban($taobao_id)
    {
        if (empty($taobao_id)) {
            return false;
        }

        $this->db->set('status', STATUS_BAN);
        $this->db->where('id', $taobao_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    function unset_taobao_ban($taobao_id)
    {
        if (empty($taobao_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->where('id', $taobao_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    // -- taobao manage part --
    function get_all_huabei_infos($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('user_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_create >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_create <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->where('huabei_status', STATUS_CHECKING);
        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_USER_BIND)->result();
    }

    function approve_huabei_info($taobao_info_id, $oper_id)
    {
        if (empty($taobao_info_id) || !is_numeric($taobao_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('huabei_status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $taobao_info_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    function reject_huabei_info($taobao_info_id, $oper_id)
    {
        if (empty($taobao_info_id) || !is_numeric($taobao_info_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('huabei_status', STATUS_FAILED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $taobao_info_id);
        return $this->db->update(self::DB_USER_BIND);
    }

    // -- Promotion manage part --
    function get_all_promotion_infos($parameters)
    {
        if (!empty($parameters['owner_id'])) {
            $this->db->where('owner_id', decode_id($parameters['owner_id']));
        }

        if (!empty($parameters['promote_id'])) {
            $this->db->where('promote_id', decode_id($parameters['promote_id']));
        }

        if (!empty($parameters['first_reward'])) {
            if($parameters['first_reward'] == 2){
                $this->db->where('first_reward', 0);
            }else{
                $this->db->where('first_reward', ($parameters['first_reward']));
            }
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('promote_time >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('promote_time <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_PROMOTE_RELATION)->result();
    }

    function delete_promotion_relation($promotion_id)
    {
        if (empty($promotion_id) || !is_numeric($promotion_id)) {
            return false;
        }

        $this->db->set('status', STATUS_DISABLE);
        $this->db->where('id', $promotion_id);
        return $this->db->update(self::DB_PROMOTE_RELATION);
    }

    // -- Top up manage part --
    function get_top_up_records($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('seller_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['member_name'])) {
            $this->db->where('seller_name', $parameters['member_name']);
        }

        if (!empty($parameters['status'])) {
            $this->db->where('status', $parameters['status']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('create_time >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('create_time <=', $parameters['end_time']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_TOP_UP_RECORD)->result();
    }

    function get_top_up_info($top_up_id)
    {
        if (empty($top_up_id) || !is_numeric($top_up_id)) {
            return false;
        }

        $this->db->where('id', $top_up_id);
        $this->db->limit(1);
        return $this->db->get(self::DB_TOP_UP_RECORD)->row();
    }

    function approve_top_up($top_up_id, $oper_id)
    {
        if (empty($top_up_id) || !is_numeric($top_up_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->set('passed_time', date('Y-m-d H:i:s'));
        $this->db->where('id', $top_up_id);
        return $this->db->update(self::DB_TOP_UP_RECORD);
    }

    function reject_top_up($top_up_id, $oper_id)
    {
        if (empty($top_up_id) || !is_numeric($top_up_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_FAILED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $top_up_id);
        return $this->db->update(self::DB_TOP_UP_RECORD);
    }

    // -- Withdraw manage part --
    function get_withdraw_records($parameters)
    {
        if (!empty($parameters['member_id'])) {
            $this->db->where('user_id', decode_id($parameters['member_id']));
        }

        if (!empty($parameters['member_name'])) {
            $this->db->where('user_name', $parameters['member_name']);
        }

        if (!empty($parameters['name'])) {
            $this->db->where('real_name', $parameters['name']);
        }

        if (!empty($parameters['status'])) {
            $this->db->where('status', $parameters['status']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('create_time >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('create_time <=', $parameters['end_time']);
        }
        if (empty($parameters['excel'])) {
            if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
                $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
            } else {
                $this->db->limit(ITEMS_PER_LOAD);
            }
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_WITHDRAW_RECORD)->result();
    }

    function get_withdraw_info($withdraw_id)
    {
        if (empty($withdraw_id) || !is_numeric($withdraw_id)) {
            return false;
        }

        $this->db->where('id', $withdraw_id);
        $this->db->limit(1);
        return $this->db->get(self::DB_WITHDRAW_RECORD)->row();
    }

    function approve_withdraw($withdraw_id, $oper_id)
    {
        if (empty($withdraw_id) || !is_numeric($withdraw_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $withdraw_id);
        if ($this->db->update(self::DB_WITHDRAW_RECORD)) {
            return $this->update_withdraw_time($withdraw_id, 'transfer_time');
        }
        return false;
    }

    function batch_approve_withdraw($withdraw_ids, $oper_id)
    {
        if (empty($withdraw_ids) || !is_array($withdraw_ids) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_PASSED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where_in('id', $withdraw_ids);
        if ($this->db->update(self::DB_WITHDRAW_RECORD)) {
            return $this->batch_update_withdraw_time($withdraw_ids, 'transfer_time');
        }
        return false;
    }


    function reject_withdraw($withdraw_id, $oper_id)
    {
        if (empty($withdraw_id) || !is_numeric($withdraw_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', STATUS_FAILED);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $withdraw_id);
        if ($this->db->update(self::DB_WITHDRAW_RECORD)) {
            return $this->update_withdraw_time($withdraw_id, 'reject_time');
        }
        return false;
    }

    public function update_withdraw_time($withdraw_id, $field)
    {
        $this->db->set($field, time());
        $this->db->where('withdraw_id', $withdraw_id);
        return $this->db->update(self::DB_WITHDRAW_TIME);
    }

    public function batch_update_withdraw_time($withdraw_ids, $field)
    {
        $this->db->set($field, time());
        $this->db->where_in('withdraw_id', $withdraw_ids);
        return $this->db->update(self::DB_WITHDRAW_TIME);
    }

    function add_oper_log($user_id, $oper_id, $oper_name, $oper_type, $oper_type_sub, $oper_content){
        $oper_data = [
            'user_id'       => $user_id,
            'oper_id'       => $oper_id,
            'oper_name'     => $oper_name,
            'oper_type'     => $oper_type,
            'oper_type_sub'     => $oper_type_sub,
            'oper_content'  => $oper_content,
            'ctime'         => time(),
        ];
        return $this->db->insert(self::DB_OPERATION_LOG, $oper_data);
    }

    //绑定一条新的店铺信息到新的商家下面
    public function add_shop_info($data)
    {
         if(empty($data)){
            return false;
         }

         $shop_data = [
             'seller_id' => $data['new_seller_id'],
             'platform_type' => $data['data']->platform_type,
             'oper_id' => $data['data']->oper_id,
             'shop_type' =>  $data['data']->shop_type,
             'shop_name' => $data['data']->shop_name,
             'shop_url' =>  $data['data']->shop_url,
             'shop_ww' =>  $data['data']->shop_ww,
             'shop_province' =>  $data['data']->shop_province,
             'shop_city' =>  $data['data']->shop_city,
             'shop_county' =>  $data['data']->shop_county,
             'shop_address' =>  $data['data']->shop_address,
             'shop_pic' =>  $data['data']->shop_pic,
             'seller_to_nick_interval' =>  $data['data']->seller_to_nick_interval,
             'seller_to_buyer_interval' =>  $data['data']->seller_to_buyer_interval,
             'shop_to_nick_interval' =>  $data['data']->shop_to_nick_interval,
             'shop_to_buyer_interval' =>  $data['data']->shop_to_buyer_interval,
             'goods_to_nick_interval' =>  $data['data']->goods_to_nick_interval,
             'goods_to_buyer_interval' =>  $data['data']->goods_to_buyer_interval,
             'shop_liuliang_interval' =>  $data['data']->shop_liuliang_interval,
             'shop_add_cart_interval' =>  $data['data']->shop_add_cart_interval,
             'gmt_create' =>  $data['data']->gmt_create,
             'gmt_update' =>  $data['data']->gmt_update,
             'status' =>  $data['data']->status,
         ];

        $this->db->insert(self::DB_SHOP_BIND, $shop_data);
        return $this->db->insert_id();
    }

    //获取该店铺下面所有的模板信息
    public function templates_info($shop_id)
    {
        if(empty($shop_id)){
            return false;
        }
        $this->db->where('shop_id', $shop_id);
        return $this->db->get(self::DB_TASK_TEMPLATES)->result();
    }

    public function add_templates($shop_id,$seller_id,$data)
    {
        if(empty($shop_id) || empty($data) || empty($seller_id)){
            return false;
        }

        $len = count($data);
        foreach($data as $k =>$v){
            $j = 0;
               $array = array(
                   'seller_id'             => $seller_id,
                   'platform_type'         => $v->platform_type,
                   'shop_id'               => $shop_id,
                   'template_name'         => $v->template_name,
                   'device_type'           => $v->device_type,
                   'item_id'               => $v->item_id,
                   'item_title'            => $v->item_title,
                   'item_display_price'    => $v->item_display_price,
                   'item_url'              => $v->item_url,
                   'item_pic'              => $v->item_pic,
                   'template_note'         => $v->template_note,
                   'gmt_create'             =>$v->gmt_create,
                   'gmt_update'            =>$v->gmt_update,
                   'status'                => $v->status,
               );
               $this->db->insert(self::DB_TASK_TEMPLATES, $array);
               $j++;
        }
        if($j >= 1){
            return true;
        }else{
            return false;
        }
    }
}