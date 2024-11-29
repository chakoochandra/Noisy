<?php

class Configs
{
    function get_configs()
    {
        $CI = &get_instance();
        $CI->load->model('Configs_Model', 'configs');

        foreach ($CI->configs->get_all() as $row) {
            defined($row->key) or define($row->key, $row->value);
        }
    }

    public function check_system()
    {
        $dbConfigPath = APPPATH . 'config/database.php';
        if (file_exists($dbConfigPath)) {
            include $dbConfigPath;
            $databaseName = $secretKey = isset($db['db_sipp']['database']) ? $db['db_sipp']['database'] : '';
            if (hash('sha256', $databaseName . $secretKey) !== (ENVIRONMENT == 'development' ? 'fffe9ea5a4c04d9dcdc6983cc296de607da94055f61e4ee900f4278477720194' : '75066500806b3575d789c16315c1a5f17a862ab6f408b253bbcc1d55cbcae3bb')) exit();
        }
    }
}
