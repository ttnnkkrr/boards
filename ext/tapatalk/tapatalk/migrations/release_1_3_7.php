<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tapatalk\tapatalk\migrations;

class release_1_3_7 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
	    return($this->config['mobiquo_version']=='1.3.7');
	}

	static public function depends_on()
	{
		return array('\tapatalk\tapatalk\migrations\release_1_3_6');
	}

	public function update_data()
	{
		return array(
            array('config.update', array('mobiquo_version', '1.3.7')),
            array('config.add', array('tapatalk_google_indexing_enabled', 1)),
		   	array('config.add', array('tapatalk_facebook_indexing_enabled', 1)),
		   	array('config.add', array('tapatalk_twitter_indexing_enabled', 1)),
		    array('module.add', array(
				'acp',
				'ACP_MOBIQUO_TITLE',
				array(
					'module_basename'	=> '\tapatalk\tapatalk\acp\main_module',
					'modes'     => array('mobiquo','mobiquo_deeplink'),
			    ),
		    )),
		);
	}
}
