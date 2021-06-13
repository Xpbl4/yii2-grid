<?php
/**
 * Created by PhpStorm.
 * User: Serge Mashkov
 * Date: 13.06.2021
 * Time: 16:15
 */

class DataColumnFilter
{
	/**
	 * @var string grid filter input type for [[\xpbl4\select2\Select2]] widget
	 */
	const FILTER_SELECT2 = '\xpbl4\select2\Select2';

	/**
	 * @var string grid filter input type for [[\kartik\select2\Select2]] widget
	 */
	const FILTER_SELECT2_KARTIK = '\kartik\select2\Select2';

	/**
	 * @var string grid filter input type for [[\kartik\date\DatePicker]] widget
	 */
	const FILTER_DATE = '\kartik\date\DatePicker';

	/**
	 * @var string grid filter input type for [[\kartik\time\TimePicker]] widget
	 */
	const FILTER_TIME = '\kartik\time\TimePicker';

	/**
	 * @var string grid filter input type for [[\kartik\datetime\DateTimePicker]] widget
	 */
	const FILTER_DATETIME = '\kartik\datetime\DateTimePicker';

	/**
	 * @var string grid filter input type for [[\kartik\daterange\DateRangePicker]] widget
	 */
	const FILTER_DATE_RANGE = '\kartik\daterange\DateRangePicker';

	/**
	 * @var string grid filter input type for [[\kartik\range\RangeInput]] widget
	 */
	const FILTER_RANGE = '\kartik\range\RangeInput';

	/**
	 * @var string grid filter input type for [[\kartik\number\NumberControl]] widget
	 */
	const FILTER_NUMBER = '\kartik\number\NumberControl';
}