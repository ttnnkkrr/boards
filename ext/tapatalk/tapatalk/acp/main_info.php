<?php
/**
* @package module_install
*/

namespace tapatalk\tapatalk\acp;

class main_info
{
    function module()
    {     
        return array(
            'filename'  => '\tapatalk\tapatalk\acp\main_module',
            'title'     => 'Tapatalk',    
        	'version'	=> '1.3.8',  
            'modes'     => array(
            	'mobiquo'  => array(
            		'title' => 'ACP_MOBIQUO_SETTINGS', 
            		'auth' => 'ext_tapatalk/tapatalk && acl_a_board', 
            		'cat' => array('ACP_MOBIQUO')
        		),
                'mobiquo_deeplink' => array(
                    'title'	=> 'ACP_TAPATALK_DEEPLINK',
                    'auth'  => 'ext_tapatalk/tapatalk && acl_a_board',
                    'cat'   => array('ACP_MOBIQUO')
                ),
                //'mobiquo_register' => array(
                //    'title'	=> 'ACP_MOBIQUO_REGISTER_SETTINGS',
                //    'auth'  => 'ext_tapatalk/tapatalk && acl_a_board',
                //    'cat'   => array('ACP_MOBIQUO')
                //)
            ),
        );
    }
}
