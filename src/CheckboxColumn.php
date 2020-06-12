<?php
/**
 * Created by PhpStorm.
 * User: Serge Mashkov
 * Date: 23/06/2018
 * Time: 11:25
 */

namespace xpbl4\grid;


use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 *
 * To add a CheckboxColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\CheckboxColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be
 * obtained by calling the following JavaScript code:
 *
 * ```javascript
 * var keys = $('#grid').yiiGridView('getSelectedRows');
 * // keys is an array consisting of the keys associated with the selected rows
 * ```
 *
 * For more details and usage information on CheckboxColumn, see the [guide article on data widgets](guide:output-data-widgets).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CheckboxColumn extends \yii\grid\CheckboxColumn
{

	/**
	 * Renders the header cell.
	 */
	public function renderHeaderCell()
	{
		$_headerOptions = ['class' => 'checkbox-column'];
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
