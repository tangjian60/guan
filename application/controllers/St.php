<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class St extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $msg = 'Timezone: ' . date_default_timezone_get();
            $msg .= ', Server time: ' . date('Y-m-d h:i:s');
            $msg .= ', DB Platform: ' . $this->db->platform();
            $msg .= ':' . $this->db->version();
            $iredis = new Redis();
            $iredis->connect(REDIS_SERVER, REDIS_PORT);
            $msg .= ', Redis says: ' . $iredis->ping();
            echo build_response_str(STATUS_ENABLE, $msg);
        } catch (Exception $e) {
            echo build_response_str(STATUS_DISABLE, $e->getMessage());
        }
    }
}