<?php
declare(ENCODING = 'utf-8');

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package FLOW3
 * @subpackage AOP
 * @version $Id$
 */

/**
 * This composite allows to check for match against a row pointcut filters
 * by only one method call. All registered filters will be invoked and if one filter
 * doesn't match, the overall result is "no".
 *
 * @package FLOW3
 * @subpackage AOP
 * @version $Id:F3_FLOW3_AOP_PointcutFilterComposite.php 201 2007-03-30 11:18:30Z robert $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @see F3_FLOW3_AOP_PointcutExpressionParser, F3_FLOW3_AOP_PointcutClassFilter, F3_FLOW3_AOP_PointcutMethodFilter
 */
class F3_FLOW3_AOP_PointcutFilterComposite implements F3_FLOW3_AOP_PointcutFilterInterface {

	/**
	 * @var array An array of F3_FLOW3_AOP_Pointcut*Filter objects
	 */
	protected $filters = array();

	/**
	 * Checks if the specified class and method match the registered class-
	 * and method filter patterns.
	 *
	 * @param F3_FLOW3_Reflection_Class $class The class to check against
	 * @param F3_FLOW3_Reflection_Method $method The method to check against
	 * @param mixed $pointcutQueryIdentifier Some identifier for this query - must at least differ from a previous identifier. Used for circular reference detection.
	 * @return boolean TRUE if class and method match the pattern, otherwise FALSE
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function matches(F3_FLOW3_Reflection_Class $class, F3_FLOW3_Reflection_Method $method, $pointcutQueryIdentifier) {
		$matches = TRUE;
		foreach ($this->filters as $operatorAndFilter) {
			list($operator, $filter) = $operatorAndFilter;
			switch ($operator) {
				case '&&' :
					$matches = $matches	&& $filter->matches($class, $method, $pointcutQueryIdentifier);
				break;
				case '&&!' :
					$matches = $matches	&& (!$filter->matches($class, $method, $pointcutQueryIdentifier));
				break;
				case '||' :
					$matches = $matches || $filter->matches($class, $method, $pointcutQueryIdentifier);
				break;
				case '||!' :
					$matches = $matches || (!$filter->matches($class, $method, $pointcutQueryIdentifier));
				break;
			}
		}
		return $matches;
	}

	/**
	 * Adds a class filter to the composite
	 *
	 * @param string$operator The operator for this filter
	 * @param F3_FLOW3_AOP_PointcutFilterInterface $classFilter A configured class filter
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function addFilter($operator, F3_FLOW3_AOP_PointcutFilterInterface $filter) {
		$this->filters[] = array($operator, $filter);
	}
}
?>