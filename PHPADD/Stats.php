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

class PHPADD_Stats
{
	public function getStats(PHPADD_Result_Analysis $mess)
	{
		$fileNo = count($mess->getFiles());

		list ($total, $regular, $missing, $outdated) = $this->analyze($mess->getResults());
		$regularFreq = $this->getFrequency($regular, $total);
		$missingFreq = $this->getFrequency($missing, $total);
		$outdatedFreq = $this->getFrequency($outdated, $total);

		return array(
			'files' => $fileNo,
			'methods' => $total,

			'regular' => $regular,
			'regular-f' => $regularFreq,

			'missing' => $missing,
			'missing-f' => $missingFreq,

			'outdated' => $outdatedFreq,
			'outdated-f' => $outdatedFreq,
		);
	}

	private function getFrequency($partial, $total)
	{
		return number_format($partial / $total * 100, 1);
	}

	private function analyze(array $results)
	{
		$regularMethods = 0;
		$missingMethods = 0;
		$outdatedMethods = 0;

		foreach ($results as $class => $methods) {
			$regularMethods += $methods->getRegularBlocks();
			$missingMethods += count($methods->getMissingBlocks());
			$outdatedMethods += count($methods->getOutdatedBlocks());
		}

		$total = $regularMethods + $missingMethods + $outdatedMethods;

		return array($total, $regularMethods, $missingMethods, $outdatedMethods);
	}
}