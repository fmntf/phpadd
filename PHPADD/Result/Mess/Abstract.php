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

abstract class PHPADD_Result_Mess_Abstract {
	protected $detail;
	protected $reflection;

	public function __construct(ReflectionMethod $reflection, array $detail)
	{
		$this->reflection = $reflection;
		$this->detail = $detail;
	}

	public function getName()
	{
		return $this->reflection->getName();
	}

	public function hasDocBlock() {
		$docBlock = $this->reflection->getDocComment();
		return (! empty ($docBlock));
	}

	public function getDocBlockStartLine() {
		/*
		 * @TODO: the start line for docblocks can be incorrect
		 * This is a very rude check of getting the start line for docblocks.
		 * It doesn't work if there are any additional spaces between the docblock
		 * and the actual function define.
		 */
		$docBlock = $this->reflection->getDocComment();
		if (empty ($docBlock)) return 0;
		
		$lc = substr_count($docBlock, "\n");
		return $this->reflection->getStartLine() - $lc - 1;
	}

	public function getStartLine() {
		return $this->reflection->getStartLine();
	}

	public function getDetail()
	{
		return $this->detail;
	}

	abstract public function toList();
}