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
	protected $_file_element;
	protected $_class_element;

	public function __construct ($argument) {
		parent::__construct($argument);
		$this->_dom = new DomDocument('1.0');
	}
	
	public function output($output) {
		// Output is not used. We use our internal _dom class

		$this->_dom->formatOutput = true;
		$this->_dom->save($this->filename);
	}

	public function publish(array $mess)
	{
		foreach ($mess as $file => $classes) {
			$attributes = array ("name" => $file);
			$this->_file_element = $this->createXMLElement('file', $attributes);

			foreach ($classes as $class => $methods) {
				$attributes = array ("name" => $class);
				$this->_class_element = $this->createXMLElement('class', $attributes);
				$this->processMethods($file, $class, $methods);
				$this->_file_element->appendChild($this->_class_element);
			}
			$this->_dom->appendChild($this->_file_element);
		}

		$this->output("");
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

	protected function processMethods($file, $class, $methods)
	{
		$output = '';

		foreach ($methods as $method)
		{
			$attributes = array ("name" => $method['method']);
			$method_element = $this->createXMLElement('method', $attributes);

			switch ($method['type']) {
				case 'miss':
					$tmp = $this->_dom->createElement('state', 'missing');
					$method_element->appendChild($tmp);
					break;
				case 'invalid':
					$tmp = $this->_dom->createElement('state', 'invalid');
					$method_element->appendChild($tmp);

					$details_element = $this->_dom->createElement('details');
					foreach ($method['detail'] as $issue) {
						$attributes = array ();
						$attributes['type'] = $this->getType($issue['type']);
						$attributes['name'] = $issue['name'];
						$detail = $this->createXMLElement('detail', $attributes);
						$details_element->appendChild($detail);
					}

					$method_element->appendChild($details_element);

					break;
			}

			$this->_class_element->appendChild($method_element);
		}

		return $output;
	}

	protected function getHeader() {
		return "";
	}

	protected function getFooter() {
		return "";
	}


}