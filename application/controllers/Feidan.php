<?php
/**
 * 飞单买号查询
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-10-16
 * Time: 上午9:59
 */
class Feidan extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    // 飞单买号查询
    public function index()
    {
        $checkDate = $this->input->get('checkDate', TRUE);
        $this->Data['data'] = [];
        if ($checkDate) {
            $this->load->model('Statistics');
            $this->Data['data'] = $this->Statistics->getFeiDanList($checkDate);
        }
        $this->Data['checkDate'] = $checkDate;

        $this->Data['PageTitle'] = '飞单买号查询';
        $this->Data['TargetPage'] = 'feidan/index';
        $this->load->view('frame_main', $this->Data);
    }


}