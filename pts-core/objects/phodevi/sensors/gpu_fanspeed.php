<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009 - 2011, Phoronix Media
	Copyright (C) 2009 - 2011, Michael Larabel

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class gpu_fanspeed implements phodevi_sensor
{
	public static function get_type()
	{
		return 'gpu';
	}
	public static function get_sensor()
	{
		return 'fan-speed';
	}
	public static function get_unit()
	{
		return 'Percent';
	}
	public static function support_check()
	{
		$test = self::read_sensor();
		return is_numeric($test) && $test != -1;
	}
	public static function read_sensor()
	{
		// Report graphics processor fan speed as a percent
		$fan_speed = -1;

		if(phodevi::is_nvidia_graphics())
		{
			// NVIDIA fan speed reading support in NVIDIA 190.xx and newer
			// TODO: support for multiple fans, also for reading GPUFanTarget to get appropriate fan
			// nvidia-settings --describe GPUFanTarget 
			$fan_speed = phodevi_parser::read_nvidia_extension('[fan:0]/GPUCurrentFanSpeed');
		}
		else if(phodevi::is_ati_graphics() && phodevi::is_linux())
		{
			$fan_speed = phodevi_linux_parser::read_ati_overdrive('FanSpeed');
		}

		return $fan_speed;
	}
}

?>
