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
 * @author  Joshua Thijssen
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_Publisher_Delim extends PHPADD_Publisher_Abstract
{
	public function publish(PHPADD_Result_Analysis $mess)
	{
		$output = "";
		
		foreach ($mess->getFiles() as $file) {
			foreach ($file->getClasses() as $class) {
				foreach ($class->getMethods() as $method) {
					if ($method instanceof PHPADD_Result_Mess_MissingBlock) {
						$output .= sprintf ("%s\t%s:%d\t%s:%d\t%s\n",
							$file->getName(), $class->getName(), $class->getStartline(),
							$method->getName(),  $method->getStartline(), 'Missing block');
					} else {
						foreach ($method->getDetail() as $detail) {
							$output .= sprintf ("%s\t%s:%d\t%s:%d\t%s\t%s\n",
								$file->getName(), $class->getName(), $class->getStartline(),
								$method->getName(), $method->getStartline(), $detail['type'], $detail['name']);
						}
					}
				}
			}
		}

		file_put_contents($this->destination, $output);
	}

}