<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Nexmo Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class DataProvider_Nexmo extends DataProvider {

	/**
	 * Client to talk to the Nexmo API
	 *
	 * @var NexmoMessage
	 */
	private $_client;

	/**
	 * @return mixed
	 */
	public function send($to, $message)
	{
		include_once Kohana::find_file('vendor', 'nexmo/NexmoMessage');

		if ( ! isset($this->_client))
		{
			$this->_client = new NexmoMessage($this->_options['api_key'], $this->_options['api_secret']);
		}

		// Send!
		try
		{
			$info = $this->_client->sendText('+'.$to, '+'.preg_replace("/[^0-9,.]/", "", $this->_from), $message);
			foreach ( $info->messages as $message )
			{
				if ( $message->status != 0)
				{
					Kohana::$log->add(Log::ERROR, 'Nexmo: '.$message->errortext);
					return array(Message_Status::FAILED, FALSE);
				}

				return array(Message_Status::SENT, $message->messageid);
			}
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}

		return array(Message_Status::FAILED, FALSE);
	}
}