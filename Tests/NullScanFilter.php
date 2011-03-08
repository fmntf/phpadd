<?php

class Tests_NullScanFilter implements PHPADD_Filterable
{
	public function isFiltered($filterableElement)
	{
		return false;
	}
}