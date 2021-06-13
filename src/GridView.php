<?php
/**
 * Created by PhpStorm.
 * User: Serge Mashkov
 * Date: 2020-06-11
 * Time: 11:35
 */

namespace xpbl4\grid;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * The GridView widget is used to display data in a grid.
 *
 * It provides features like [[sorter|sorting]], [[pager|paging]] and also [[filterModel|filtering]] the data.
 *
 * A basic usage looks like the following:
 *
 * ```php
 * <?= GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         'id',
 *         'name',
 *         'created_at:datetime',
 *         // ...
 *     ],
 * ]) ?>
 * ```
 *
 * The columns of the grid table are configured in terms of [[Column]] classes,
 * which are configured via [[columns]].
 *
 * The look and feel of a grid view can be customized using the large amount of properties.
 *
 * For more details and usage information on GridView, see the [guide article on data widgets](guide:output-data-widgets).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridView extends \yii\grid\GridView
{
	const ERROR_POS_FILTER = 'filter';
	const ERROR_POS_SUMMARY = 'summary';
	const ERROR_POS_TOOLTIP = 'tooltip';

	/**
	 * @var string the default data column class if the class name is not explicitly specified when configuring a data column.
	 * Defaults to 'yii\grid\DataColumn'.
	 */
	public $dataColumnClass;
	/**
	 * @var array the HTML attributes for the table header row.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $headerRowOptions = ['class' => 'sorting'];
	/**
	 * @var string whether the filter errors should be displayed in the grid view. Valid values include:
	 *
	 * - [[ERROR_POS_FILTER]]: the errors will be displayed right below each column's filter input.
	 * - [[ERROR_POS_SUMMARY]]: the errors will be displayed in the filter model {errors} section. See [[renderErrors()]].
	 * - [[ERROR_POS_TOOLTIP]]: the errors will be displayed in tooltip of each column's filter input.
	 */
	public $filterErrorPosition = self::ERROR_POS_TOOLTIP;

	/**
	 * @var string whether the filters should be displayed in the grid view. Valid values include:
	 *
	 * - [[FILTER_POS_HEADER]]: the filters will be replace of each column's header cell.
	 * - [[FILTER_POS_BODY]]: the filters will be displayed right below each column's header cell.
	 * - [[FILTER_POS_FOOTER]]: the filters will be displayed below each column's footer cell.
	 */
	public $filterPosition = self::FILTER_POS_BODY;

	/**
	 * @var array the HTML attributes for the filter row element.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $filterRowOptions = ['class' => 'filters form-group form-group-sm'];

	/**
	 * Renders the table header.
	 * @return string the rendering result.
	 */
	public function renderTableHeader()
	{
		$cells = [];
		foreach ($this->columns as $column) {
			/* @var $column \yii\grid\Column */
			$cells[] = $column->renderHeaderCell();
		}
		$content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);

		if ($this->filterPosition === self::FILTER_POS_HEADER) {
			Html::addCssClass($this->filterRowOptions, $this->headerRowOptions['class']);

			$content = $this->renderFilters();
		} elseif ($this->filterPosition === self::FILTER_POS_BODY) {
			$content .= $this->renderFilters();
		}

		return "<thead>\n" . $content . "\n</thead>";
	}

	/**
	 * Renders the filter.
	 * @return string the rendering result.
	 */
	public function renderFilters()
	{
		$cells = [];
		foreach ($this->columns as $column) {
			/* @var $column \yii\grid\Column */
			/* @var $column DataColumn */
			$cellRender = null;
			if ($this->filterModel !== null) {
				if ($this->filterPosition === self::FILTER_POS_HEADER) {
					$column->filterOptions = ArrayHelper::merge($column->headerOptions, $column->filterOptions);
				}
				$cellRender = $column->renderFilterCell();
			}

			if (empty($cellRender) && $this->filterPosition === self::FILTER_POS_HEADER) {
				$cellRender = $column->renderHeaderCell();
			}

			$cells[] = $cellRender;
		}

		$filterRow = implode('', $cells);

		if (!empty($filterRow)) {
			return Html::tag('tr', $filterRow, $this->filterRowOptions);
		}

		return '';
	}


	/**
	 * Creates column objects and initializes them.
	 */
	protected function initColumns()
	{
		if (empty($this->columns)) {
			$this->guessColumns();
		}
		foreach ($this->columns as $i => $column) {
			if (is_string($column)) {
				$column = $this->createDataColumn($column);
			} else {
				$column = Yii::createObject(array_merge([
					'class' => $this->dataColumnClass ?: DataColumn::className(),
					'grid' => $this,
				], $column));
			}
			if (!$column->visible) {
				unset($this->columns[$i]);
				continue;
			}
			$this->columns[$i] = $column;
		}
	}

	/**
	 * Creates a [[DataColumn]] object based on a string in the format of "attribute:format|headerClass:label".
	 * @param string $text the column specification string
	 * @return DataColumn the column instance
	 * @throws InvalidConfigException if the column specification is invalid
	 */
	protected function createDataColumn($text)
	{
		if (!preg_match('/^([^:]+)(:([^:]+))?(:(.*))?$/', $text, $matches)) {
			throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
		}
		$_attribute = $matches[1];
		$_format = 'text';
		$_options = [];
		if (isset($matches[3])) {
			$_formatOptions = @explode('|', $matches[3]);
			$_format = $_formatOptions[0];
			if (isset($_formatOptions[1])) $_options['class'] = $_formatOptions[1];
		}
		$_label = isset($matches[5]) ? $matches[5] : null;

		return Yii::createObject([
			'class' => $this->dataColumnClass ?: DataColumn::className(),
			'grid' => $this,
			'attribute' => $_attribute,
			'format' => $_format,
			'label' => $_label,
			'headerOptions' => $_options,
		]);
	}


}