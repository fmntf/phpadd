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

class PHPADD_Publisher_Xml extends PHPADD_Publisher_Abstract
{
	public function publish(PHPADD_Result_Analysis $mess)
	{
		$dom = new DOMDocument('1.0');
		
		if (!$this->statsOnly) {
			$this->addMessToXml($mess, $dom);
		}
		$this->addStatsToXml($mess, $dom);
		
		$dom->formatOutput = true;
		$dom->save($this->destination);
	}
	
	private function addStatsToXml(PHPADD_Result_Analysis $mess, DOMDocument $dom)
	{
		$helper = new PHPADD_Stats();
		$stats = $helper->getStats($mess);
		
		$element = $this->createXMLElement($dom, 'stats', $stats);
		$dom->appendChild($element);
	}

	private function addMessToXml(PHPADD_Result_Analysis $mess, DOMDocument $dom)
	{
		foreach ($mess->getDirtyFiles() as $file) {
			$attributes = array ("name" => $file->getName());
			$file_element = $this->createXMLElement($dom, 'file', $attributes);

			foreach ($file->getClasses() as $class) {
				$attributes = array ();
				$attributes['name'] = $class->getName();
				$attributes['line'] = $class->getStartline();
				$class_element = $this->createXMLElement($dom, 'class', $attributes);

				foreach ($class->getMethods() as $method) {
					$attributes = array ("name" => $class->getName());
					$method_element = $this->createXMLElement($dom, 'method', $attributes);

					if ($method instanceof PHPADD_Result_Mess_MissingBlock) {
						$attributes = array ();
						$attributes['type'] = 'missing-docblock';
						$detail = $this->createXMLElement($dom, 'detail', $attributes);
						$method_element->appendChild($detail);
					} else {
						foreach ($method->getDetail() as $detail) {
							$attributes = array ();
							$attributes['type'] = $detail['type'];
							$attributes['name'] = $detail['name'];
							$detail = $this->createXMLElement($dom, 'detail', $attributes);
							$method_element->appendChild($detail);
						}
					}
					$class_element->appendChild($method_element);
				}

				$file_element->appendChild($class_element);
			}
			$dom->appendChild($file_element);
		}
	}

	private function createXMLElement(DOMDocument $dom, $name, $attributes)
	{
		$element = $dom->createElement($name);
		foreach ($attributes as $key => $value) {
			$attr_element = $dom->createAttribute($key);
			$attr_element->appendChild($dom->createTextNode($value));

			$element->appendChild($attr_element);
		}
		return $element;
	}
}