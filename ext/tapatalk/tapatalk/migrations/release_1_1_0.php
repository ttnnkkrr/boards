<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tapatalk\tapatalk\migrations;

class release_1_1_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
	    return($this->config['mobiquo_version']=='1.1.0');
	}

	static public function depends_on()
	{
		return array('\tapatalk\tapatalk\migrations\release_1_0_0',
            '\tapatalk\tapatalk\migrations\release_1_0_1');
	}

	public function update_data()
	{
		return array(
		     array('config.update', array('mobiquo_version', '1.1.0')),
             array('config.add', array('tapatalk_auto_approve', '1')),
			 array('config.add', array('tapatalk_twitterfacebook_card_enabled', '1')),
			 array('config.add', array('tapatalk_sso_enabled', '1')),
			 array('config.add', array('tapatalk_app_banner_enable', '1')),
			 array('module.remove', array(
            'acp',
            'ACP_MOBIQUO_TITLE',
            array(
                'module_basename'       => '\tapatalk\tapatalk\acp\main_module',
                'modes'                 => array('mobiquo_rebranding','mobiquo_register'),
            ),
        )),
		);
	}
    public function update_schema()
    {
         return array('add_tables'    => array(
            $this->table_prefix . 'tapatalk_users'        => array(
                'COLUMNS'        => array(
                        'userid'        => array('UINT:10', 0),
                        'announcement'  => array('USINT', 1),
                        'pm'            => array('USINT', 1),
                        'subscribe'     => array('USINT', 1),
                        'quote'         => array('USINT', 1),
                        'liked'         => array('USINT', 1),
                        'tag'           => array('USINT', 1),
                        'newtopic'      => array('USINT', 1),
                        'updated'       => array('TIMESTAMP'),
                ),
                'PRIMARY_KEY'        => 'userid',
            )));
    }
    public function revert_schema()
    {
         return array('drop_tables'    => array($this->table_prefix . 'tapatalk_users'));
    }
}
