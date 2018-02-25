<?php

namespace App\Libraries\Twitch;

use DB;
use App\User;

use \jofner\SDK\TwitchTV\TwitchSDK;

class Twitch
{
	public $twitch;

	public function __construct(TwitchSDK $twitch)
	{
		$this->twitch = $twitch;
	}
	public function getChannel($channel_name)
	{
		return $this->twitch->channelGet($channel_name);
	}

	public function getLiveStreamChannelNames($obj = false)
	{
		$users = User::where('twitch_channel_name', '!=', 'null')->get();
		$return = array();
		foreach ($users as $user) {
			$stream = $this->twitch->streamGet($user->twitch_channel_name);
			if ($stream->stream != null) {
				$return[] = array(
					'channel_name' 	=> $user->twitch_channel_name,
					'user_id'		=> $user->id,
					'steamname'		=> $user->steamname,
				);

			}
		}
		if ($obj) {
			return json_decode(json_encode($return), FALSE);
		}
		return $return;
	}
}