<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tapatalk\tapatalk\migrations;

class release_1_3_8 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
	    return($this->config['mobiquo_version']=='1.3.8');
	}

	static public function depends_on()
	{
		return array('\tapatalk\tapatalk\migrations\release_1_3_7');
	}

	public function update_data()
	{
		return array(
            array('config.update', array('mobiquo_version', '1.3.8')),
		);
	}
}
