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
 * RadioButtonColumn displays a column of radio buttons in a grid view.
 *
 * To add a RadioButtonColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\RadioButtonColumn',
 *         'radioOptions' => function ($model) {
 *              return [
 *                  'value' => $model['value'],
 *                  'checked' => $model['value'] == 2
 *              ];
 *          }
 *     ],
 * ]
 * ```
 *
 * @author Kirk Hansen <hanski07@luther.edu>
 * @since 2.0.11
 */
class RadioButtonColumn extends \yii\grid\RadioButtonColumn
{
	/**
	 * Renders the header cell.
	 */
	public function renderHeaderCell()
	{
		$_headerOptions = ['class' => 'radio-button-column'];
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
