<?php
/**
 * Created by PhpStorm.
 * User: Serge Mashkov
 * Date: 23/06/2018
 * Time: 11:25
 */

namespace xpbl4\grid;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * PriorityColumn displays a column of checkboxes in a grid view.
 *
 * To add a PriorityColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'xpbl4\grid\PriorityColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * Users may sort rows of the grid. The sorted rows may be saved by calling the following action: [[\xpbl4\grid\actions\PriorityAction]].
 *
 * @author Serge Mashkov
 * @since 2.0
 */
class PriorityColumn extends DataColumn
{
	/**
	 * @var array the HTML attributes for the header cell tag.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $headerOptions = ['style' => 'width: 30px;'];

	/**
	 * @var string the label for the handle
	 */
	public $handleLabel = '&#9776;';

	/**
	 * @var array the HTML attributes for the handle
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $handleOptions = ['class' => 'sortable-widget-handler'];

	/**
	 * @var string the selector for the handle
	 */
	public $handleSelector = '.sortable-widget-handler';

	/**
	 * @var array the plugin options
	 */
	public $pluginOptions = [];

	/**
	 * @var array the action url
	 *
	 * @see \yii\helpers\Url::to() for details on how to specify this parameter.
	 */
	public $actionUrl = ['priority'];

	private $sortable = true;

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		$this->sortable = $this->grid->dataProvider->getSort()->getAttributeOrder($this->attribute);
		PriorityColumnAsset::register($this->grid->getView());

		$gridHandler = '#'.$this->grid->id;
		$this->pluginOptions = ArrayHelper::merge([
			'handle' => $this->handleSelector,
			'animation' => 300,
			'dataIdAttr' => 'data-key',
			'onEnd' => new \yii\web\JsExpression("function (e) {
				var context = $('".$gridHandler."');
				$.ajax({
			        url: '".\yii\helpers\Url::to($this->actionUrl)."',
			        type: 'POST',
			        data: {
				        sorting: this.toArray(),
				        offset: $(e.item).find('[data-offset]').data('offset')
			        },
			        dataType: 'json',
			        success: function(data) {
						if (context.data('pjax'))
							$.pjax.reload({container: context.data('pjax-container'), timeout: context.data('pjax-timeout')})
			        }
				});
            }")
		], $this->pluginOptions);

		// Init widget
		$settings = \yii\helpers\Json::encode($this->pluginOptions);
		$this->grid->view->registerJs("jQuery('".$gridHandler." tbody').sortableWidget($settings);", \yii\web\View::POS_READY, 'grid-sortable-widget');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		if (!$this->sortable)
			return Html::tag('div', $this->handleLabel, ['class' => 'sortable-widget-disabled']);

		$offset = 0;
		if ($this->grid->dataProvider->pagination)
			$offset = $this->grid->dataProvider->pagination->pageSize * $this->grid->dataProvider->pagination->page;

		$handleOptions = $this->handleOptions;

		$value = $this->getDataCellValue($model, $key, $index);
		if ($value > 0) Html::addCssClass($handleOptions, 'sortable-widget-active');

		return Html::tag('div', $this->handleLabel, ArrayHelper::merge($handleOptions, [
			'data-id' => $model->getPrimaryKey(),
			'data-offset' => $offset
		]));
	}

	/**
	 * Renders the header cell.
	 */
	public function renderHeaderCell()
	{
		$_headerOptions = ['class' => 'priority-column'];
		if ($this->grid->filterModel !== null && $this->grid->filterPosition !== \yii\grid\GridView::FILTER_POS_HEADER)
			$_headerOptions['rowspan'] = '2';

		$this->label = '&nbsp;';
		$this->encodeLabel = false;
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