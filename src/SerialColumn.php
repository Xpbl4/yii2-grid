<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace xpbl4\grid;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * SerialColumn displays a column of row numbers (1-based).
 *
 * To add a SerialColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\SerialColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * For more details and usage information on SerialColumn, see the [guide article on data widgets](guide:output-data-widgets).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SerialColumn extends \yii\grid\SerialColumn
{
	/**
	 * Renders the header cell.
	 */
	public function renderHeaderCell()
	{
		$_headerOptions = ['class' => 'serial-column'];
		if ($this->grid->filterModel !== null && $this->grid->filterPosition !== \yii\grid\GridView::FILTER_POS_HEADER)
			$_headerOptions['rowspan'] = '2';

		$this->headerOptions = ArrayHelper::merge($_headerOptions, $this->headerOptions);
		return Html::tag('th', $this->renderHeaderCellContent(), $this->headerOptions);
	}

	/**
	 * Renders the filter cell.
	 */
	public function renderFilterCell()
	{
		return null;
	}
}
