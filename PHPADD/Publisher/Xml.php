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
	protected $_dom;
	protected $_class_element;

	public function __construct ($argument) {
		parent::__construct($argument);
		$this->_dom = new DomDocument('1.0');
	}

	public function publish(PHPADD_Result_Analysis $mess)
	{
		foreach ($mess->getDirtyFiles() as $file) {
			$attributes = array ("name" => $file->getName());
			$file_element = $this->createXMLElement('file', $attributes);

			foreach ($file->getClasses() as $class) {
				$attributes = array ();
				$attributes['name'] = $class->getName();
				$attributes['line'] = $class->getStartline();
				$class_element = $this->createXMLElement('class', $attributes);

				foreach ($class->getMethods() as $method) {
					$attributes = array ("name" => $class->getName());
					$method_element = $this->createXMLElement('method', $attributes);

					if ($method instanceof PHPADD_Result_Mess_MissingBlock) {
						$attributes = array ();
						$attributes['type'] = 'missing-docblock';
						$detail = $this->createXMLElement('detail', $attributes);
						$method_element->appendChild($detail);
					} else {
						foreach ($method->getDetail() as $detail) {
							$attributes = array ();
							$attributes['type'] = $detail['type'];
							$attributes['name'] = $detail['name'];
							$detail = $this->createXMLElement('detail', $attributes);
							$method_element->appendChild($detail);
						}
					}
					$class_element->appendChild($method_element);
				}

				$file_element->appendChild($class_element);
			}
			$this->_dom->appendChild($file_element);
		}


//		foreach ($mess->getResults() as $class => $methods) {
//			$attributes = array ("name" => $class);
//			$class_element = $this->createXMLElement('class', $attributes);
//
//			if (!$methods->isClean()) {
//				$element = $this->processMethods($class, $methods);
//				$class_element->appendChild($element);
//			}
//
//			$this->_dom->appendChild($class_element);
//		}

		$this->_dom->formatOutput = true;
		$this->_dom->save($this->destination);
	}

	protected function processMethods($class, PHPADD_Result_Class $methods)
	{
		$issues = array_merge($methods->getMissingBlocks(), $methods->getOutdatedBlocks());
		foreach ($issues as $method) {

			$attributes = array ("name" => $method->getName());
			$method_element = $this->createXMLElement('method', $attributes);

			$details_element = $this->_dom->createElement('details');
			foreach ($method->getDetail() as $detail) {
				$attributes = array ();
				$attributes['type'] = $detail['type'];
				$attributes['name'] = $detail['name'];
				$detail = $this->createXMLElement('detail', $attributes);
				$details_element->appendChild($detail);
			}

			$method_element->appendChild($details_element);
		}
		return $method_element;
	}

	protected function createXMLElement($name, $attributes) {
		$element = $this->_dom->createElement($name);
		foreach ($attributes as $key => $value) {
			$attr_element = $this->_dom->createAttribute($key);
			$attr_element->appendChild($this->_dom->createTextNode($value));

			$element->appendChild($attr_element);
		}
		return $element;
	}
}