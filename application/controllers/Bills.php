<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bills extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('paycore');
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['bill_type'] = $this->input->get('bill_type', TRUE);
            $this->Data['order_id'] = $this->input->get('order_id', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['start_amount'] = $this->input->get('start_amount', TRUE);
            $this->Data['end_amount'] = $this->input->get('end_amount', TRUE);

            $this->Data['data'] = $this->paycore->get_bills($this->Data);
        }

        $this->Data['TargetPage'] = 'page_bills';
        $this->load->view('frame_main', $this->Data);
    }

    public function record()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $task_id = $this->input->get('task_id', TRUE);
        $this->Data['task_id'] = decode_id($task_id);
        $buyer_id = $this->input->get('buyer_id', TRUE);
        $item_id = $this->input->get('item_id', TRUE);
        $this->Data['buyer_id'] = decode_id($buyer_id);
        $this->Data['item_id'] = $item_id;
        $this->Data['gmt_cancelled'] = $this->input->get('gmt_cancelled', TRUE);
        //$this->Data['seller_id'] = $this->get_seller_id();
        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }
       
        $this->Data['TargetPage'] = 'page_calloff_record';
        $this->Data['PageTitle'] = '买手取消任务单记录';
        $this->Data['data'] = $this->paycore->get_task_cancelled($this->Data);

        $this->load->view('frame_main', $this->Data);
    }




}