<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace xpbl4\grid;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * DataColumn is the default column type for the [[GridView]] widget.
 *
 * It is used to show data columns and allows [[enableSorting|sorting]] and [[filter|filtering]] them.
 *
 * A simple data column definition refers to an attribute in the data model of the
 * GridView's data provider. The name of the attribute is specified by [[attribute]].
 *
 * By setting [[value]] and [[label]], the header and cell content can be customized.
 *
 * A data column differentiates between the [[getDataCellValue|data cell value]] and the
 * [[renderDataCellContent|data cell content]]. The cell value is an un-formatted value that
 * may be used for calculation, while the actual cell content is a [[format|formatted]] version of that
 * value which may contain HTML markup.
 *
 * For more details and usage information on DataColumn, see the [guide article on data widgets](guide:output-data-widgets).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DataColumn extends \yii\grid\DataColumn
{
	/**
	 * @var string grid filter input type for [[\xpbl4\select2\Select2]] widget
	 */
	const FILTER_SELECT2 = '\xpbl4\select2\Select2';

	/**
	 * @var string|array|null|false the HTML code representing a filter input (e.g. a text field, a dropdown list)
	 * that is used for this data column. This property is effective only when [[GridView::filterModel]] is set.
	 *
	 * - If this property is not set, a text field will be generated as the filter input with attributes defined
	 *   with [[filterInputOptions]]. See [[\yii\helpers\BaseHtml::activeInput]] for details on how an active
	 *   input tag is generated.
	 * - If this property is an array, a dropdown list will be generated that uses this property value as
	 *   the list options.
	 * - If you don't want a filter for this data column, set this value to be false.
	 */
	public $filter;
	/**
	 * @var array the HTML attributes for the filter input fields. This property is used in combination with
	 * the [[filter]] property. When [[filter]] is not set or is an array, this property will be used to
	 * render the HTML attributes for the generated filter input fields.
	 *
	 * Empty `id` in the default value ensures that id would not be obtained from the model attribute thus
	 * providing better performance.
	 *
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $filterInputOptions = ['class' => 'form-control', 'id' => null];
	/**
	 * @var string the filter input type for each filter input. You can use one of the `DataColumn::FILTER_` constants or
	 * pass any widget classname (extending the Yii Input Widget).
	 */
	public $filterWidget;

	/**
	 * @var array the options/settings for the filter widget. Will be used only if you set `filterType` to a widget
	 * classname that exists.
	 */
	public $filterWidgetOptions = [];

	/**
	 * @var GridView the grid view object that owns this column.
	 */
	public $grid;

	/**
	 * Renders the filter cell.
	 */
	public function renderFilterCell()
	{
		$filterCellContent = $this->renderFilterCellContent();
		if (empty($filterCellContent) && $this->grid->filterPosition === GridView::FILTER_POS_HEADER) {
			$filterCellContent = $this->renderHeaderCellContent();
			return Html::tag('th', $filterCellContent, $this->filterOptions);
		}

		return Html::tag('td', $filterCellContent, $this->filterOptions);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function renderFilterCellContent()
	{
		if (is_string($this->filter)) {
			return $this->filter;
		}

		$model = $this->grid->filterModel;

		if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
			if ($model->hasErrors($this->attribute) && $this->grid->filterErrorPosition !== GridView::ERROR_POS_SUMMARY) {
				Html::addCssClass($this->filterOptions, 'has-error');
				$error = ' '.Html::error($model, $this->attribute, $this->grid->filterErrorOptions);

				if ($this->grid->filterErrorPosition == GridView::ERROR_POS_TOOLTIP) {
					$this->filterOptions = ArrayHelper::merge($this->filterOptions, [
						'data' => [
							'tooltip' => trim(strip_tags($error)),
							'tooltip-style' => 'danger tooltip-lg',
							'tooltip-align' => 'left',
							'placement' => 'auto top'
						]
					]);
					$error = '';
				}
			} else {
				$error = '';
			}

			$sorting = '';
			if ($this->grid->filterPosition === GridView::FILTER_POS_HEADER) {
				$label = $this->getHeaderCellLabel();
				if ($this->encodeLabel) {
					$label = Html::encode($label);
				}

				$this->filterInputOptions = ArrayHelper::merge($this->filterInputOptions, [
					'placeholder' => $label
				]);

				if ($this->attribute !== null && $this->enableSorting &&
					($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
					$sorting = $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => '']));
					//$sorting = Html::tag('span', $sorting, ['class' => 'input-group-addon']);
				} else {
					Html::addCssClass($this->filterOptions, 'unsorted');
				}
			}

			if ($this->filterWidget) {
				/** @var \yii\base\Widget $filterWidget */
				$filterWidget = $this->filterWidget;
				$filterWidgetOptions = [
					'model' => $this->grid->filterModel,
					'attribute' => $this->attribute,
					'options' => $this->filterInputOptions,
				];
				if (is_array($this->filter)) {
					$filterWidgetOptions = ArrayHelper::merge($filterWidgetOptions, [
						'items' => $this->filter,
						'options' => ['prompt' => '']
					], $filterWidgetOptions);
				}
				$filterWidgetOptions = ArrayHelper::merge($filterWidgetOptions, $this->filterWidgetOptions);

				return $sorting.$filterWidget::widget($filterWidgetOptions).$error;
			}

			if (is_array($this->filter)) {
				$options = array_merge(['prompt' => ''], $this->filterInputOptions);
				return $sorting.Html::activeDropDownList($model, $this->attribute, $this->filter, $options).$error;
			} elseif ($this->format === 'boolean') {
				$options = array_merge(['prompt' => ''], $this->filterInputOptions);
				return $sorting.Html::activeDropDownList($model, $this->attribute, [
						1 => $this->grid->formatter->booleanFormat[1],
						0 => $this->grid->formatter->booleanFormat[0],
					], $options).$error;
			}

			return $sorting.Html::activeTextInput($model, $this->attribute, $this->filterInputOptions).$error;
		}

		if ($this->grid->filterPosition === GridView::FILTER_POS_HEADER)
			return null;

		return parent::renderFilterCellContent();
	}
}
