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

	const LAYOUT_DEFAULT = "{summary}\n{items}\n{pager}";
	const LAYOUT_DATA_TABLES = <<<LAYOUT
	<div class="row"><div class="col-sm-12">{toolbar}</div></div>
	<div class="row"><div class="col-sm-12">{items}</div></div>
    <div class="row">
        <div class="col-sm-5">
            <div class="table-grid-info">{summary}</div>
        </div>
        <div class="col-sm-7">
            <div class="table-grid-paginate">{pager}</div>
        </div>
    </div>
LAYOUT;

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
	 * - [[FILTER_POS_HEADER]]: the filters will be replaced of each column's header cell.
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
	 * @var array the HTML attributes for the grid table element.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $tableOptions = ['class' => 'table table-grid table-bordered table-hover table-update'];

	/**
	 * @var array the HTML attributes for the container tag of the grid view.
	 * The "tag" element specifies the tag name of the container element and defaults to "div".
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $options = ['class' => 'table-grid-wrapper dt-bootstrap'];

	/**
	 * @var string the layout that determines how different sections of the grid view should be organized.
	 * The following tokens will be replaced with the corresponding section contents:
	 *
	 * - `{summary}`: the summary section. See [[renderSummary()]].
	 * - `{errors}`: the filter model error summary. See [[renderErrors()]].
	 * - '{buttons}': the grid action buttons. See [[renderButtons()]].
	 * - '{toolbar}': the toolbar. See [[renderToolbar()]].
	 * - '{entries}': the entries count. See [[renderEntries()]].
	 * - `{items}`: the list items. See [[renderItems()]].
	 * - `{sorter}`: the sorter. See [[renderSorter()]].
	 * - `{pager}`: the pager. See [[renderPager()]].
	 */
	public $layout = self::LAYOUT_DATA_TABLES;

	/**
	 * @var array the configuration for the pager widget. By default, [[LinkPager]] will be
	 * used to render the pager. You can use a different widget class by configuring the "class" element.
	 * Note that the widget must support the `pagination` property which will be populated with the
	 * [[\yii\data\BaseDataProvider::pagination|pagination]] value of the [[dataProvider]] and will overwrite this value.
	 */
	public $pager = ['class' => \yii\widgets\LinkPager::class, 'options' => ['class' => 'pagination pagination-sm']];

	/** @var int[] the list for the pagination widget shows how many items should be showed on page.
	 *  if -1 is set, all items will be showed
	 */
	public $pageSizeList = [10, 20, 50, 100, 0];

	/**
	 * @var array list of buttons. Each array element represents a single button
	 * which can be specified as a string or an array of the following structure:
	 *
	 * - label: string, required, the button label.
	 * - options: array, optional, the HTML attributes of the button.
	 * - visible: bool, optional, whether this button is visible. Defaults to true.
	 */
	public $buttons = [];

	/**
	 * @var array the HTML attributes for the buttons container tag.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $toolbarLayout = '<div class="pull-left">{buttons}</div><div class="pull-right">{entries}</div>';

	public function init()
	{
		parent::init();

		if (($pagination = $this->dataProvider->getPagination()) !== false) {
			$min = min($this->pageSizeList);
			$totalCount = $this->dataProvider->getTotalCount();
			if ($min <= 0 && $totalCount > 1000) $min = 10;

			$pagination->pageSizeLimit = [$min, min(1000, max($this->pageSizeList))];
			$this->dataProvider->setPagination($pagination);
		}
	}

	public function run()
	{
		$view = $this->getView();
		GridViewAsset::register($view);

		parent::run();
	}


	public function renderButtons()
	{
		if (!empty($this->buttons)) return \yii\bootstrap\ButtonGroup::widget([
			'buttons' => $this->buttons,
			'options' => ['class' => 'btn-group-sm'],
			'encodeLabels' => false
		]);

		return '';
	}

	public function renderEntries()
	{
		return \yii\helpers\Html::tag('div', $this->renderEntriesContent(), ['class' => 'table-grid-length form-group form-group-sm']);
	}

	public function renderEntriesContent()
	{
		$pagination = $this->dataProvider->getPagination();
		if ($pagination === false || $this->dataProvider->getCount() <= 0) {
			return '';
		}

		$page = $pagination->getPage();
		$pageSize = $pagination->getPageSize();
		$totalCount = $this->dataProvider->getTotalCount();
		$items = [];
		foreach ($this->pageSizeList as $value) {
			if ($value <= 0 && $totalCount > 1000) continue;
			$items[] = $value > 0 ?
				['label' => $value, 'url' => $pagination->createUrl(floor($page * $pageSize / $value), $value), 'options' => $value == $pageSize ? ['class' => 'active'] : []] :
				['label' => 'All', 'url' => str_replace('per-page=1', 'per-page=0', $pagination->createUrl(0, 1))];
		}

		return \yii\bootstrap\ButtonDropdown::widget([
			'label' => Yii::t('yii', 'Show {size, plural, =0{all items} =1{one item} other{# items}}', ['size' => $pageSize]),
			'split' => true,
			'options' => ['class' => 'btn btn-default btn-sm'],
			'dropdown' => [
				'items' => $items,
				'options' => ['class' => 'dropdown-menu-right dropdown-menu-auto'],
			],
		]);
	}

	public function renderToolbar()
	{
		if (!empty($this->toolbarLayout)) {
			return preg_replace_callback('/{\\w+}/', function ($matches) {
				$content = $this->renderSection($matches[0]);

				return $content === false ? $matches[0] : $content;
			}, $this->toolbarLayout);
		}

		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderSection($name)
	{
		switch ($name) {
			case '{toolbar}':
				return $this->renderToolbar();
			case '{buttons}':
				return $this->renderButtons();
			case '{entries}':
				return $this->renderEntries();
			default:
				return parent::renderSection($name);
		}
	}

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