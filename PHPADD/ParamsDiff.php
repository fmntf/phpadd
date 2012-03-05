<?php

/**
 * phpadd - abandoned docblocks detector
 * Copyright (C) 2010 Francesco Montefoschi <francesco.monte@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package phpadd
 * @author  Francesco Montefoschi
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_ParamsDiff
{
	public function diff(array $php, array $block)
	{
		$phpParams = $this->prepare($php);
		$blockParams = $this->prepare($block);
		
		$result = array();
		
		$missing = array_values(array_diff(array_keys($phpParams), array_keys($blockParams)));
		foreach ($missing as $param) {
			$result[] = new PHPADD_Result_Mess_MissingParam($param, $phpParams[$param]);
			unset($phpParams[$param]);
		}
		
		$unexpected = array_values(array_diff(array_keys($blockParams), array_keys($phpParams)));
		foreach ($unexpected as $param) {
			$result[] = new PHPADD_Result_Mess_UnexpectedParam($param, $blockParams[$param]);
			unset($blockParams[$param]);
		}
		
		foreach ($phpParams as $param => $phpType) {
			$blockType = $blockParams[$param];
			if ($phpType != $blockType) {
				
				if ($blockType !== '\\'.$phpType &&
					!$this->endsWith($phpType, $blockType)
				) {
					$result[] = new PHPADD_Result_Mess_OutdatedParam($param, $phpType, $blockParams[$param]);
				}
			}
		}
		
		return $result;
	}
	
	private function prepare(array $params)
	{
		$result = array();
		
		foreach ($params as $param) {
			$parts = explode(' ', $param);
			if (count($parts) == 2) {
				$result[$parts[1]] = $parts[0];
			} else {
				$result[$parts[0]] = null;
			}
		}
		
		return $result;
	}
	
	private function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}
}