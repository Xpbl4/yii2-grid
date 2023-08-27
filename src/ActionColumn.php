<?php
/**
 * Created by PhpStorm.
 * User: Serge Mashkov
 * Date: 22/05/17
 * Time: 16:31
 */

namespace xpbl4\grid;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 *
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => ActionColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * For more details and usage information on ActionColumn, see the [guide article on data widgets](guide:output-data-widgets).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ActionColumn extends \yii\grid\ActionColumn
{
	/**
	 * @var string the template used for composing each cell in the action column.
	 * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names*
	 * in the context of action column). They will be replaced by the corresponding button rendering callbacks
	 * specified in [[buttons]]. For example, the token `{view}` will be replaced by the result of
	 * the callback `buttons['view']`. If a callback cannot be found, the token will be replaced with an empty string.
	 *
	 * As an example, to only have the view, and update button you can add the ActionColumn to your GridView columns as follows:
	 *
	 * ```php
	 * ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
	 * ```
	 *
	 * @see buttons
	 */
	public $template = '{view} {update} {delete}';

	/**
	 * @var string the header cell content. Note that it will not be HTML-encoded.
	 */
	public $header = 'Actions';

	/**
	 * @var array html options to be applied to the [[initDefaultButton()|default button]].
	 * @since 2.0.4
	 */
	public $buttonOptions = ['class' => 'btn btn-default'];

	/**
	 * @var array html options to be applied to the [[initDefaultButton()|view button]].
	 * @since 2.0.4
	 */
	public $viewOptions = [];

	/**
	 * @var string The part of Bootstrap glyphicon class that makes it unique
	 * @since 2.0.4
	 */
	public $viewIcon = 'eye-open';


	/**
	 * @var array html options to be applied to the [[initDefaultButton()|update button]].
	 * @since 2.0.4
	 */
	public $updateOptions = [];

	/**
	 * @var string The part of Bootstrap glyphicon class that makes it unique
	 * @since 2.0.4
	 */
	public $updateIcon = 'pencil';

	/**
	 * @var array html options to be applied to the [[initDefaultButton()|delete button]].
	 * @since 2.0.4
	 */
	public $deleteOptions = ['class' => 'btn btn-danger'];

	/**
	 * @var string The part of Bootstrap glyphicon class that makes it unique
	 * @since 2.0.4
	 */
	public $deleteIcon = 'trash';


	/**
	 * Initializes the default button rendering callbacks.
	 */
	protected function initDefaultButtons()
	{
		$this->initDefaultButton('view', $this->viewIcon, $this->viewOptions);
		$this->initDefaultButton('update', $this->updateIcon, $this->updateOptions);
		$this->initDefaultButton('delete', $this->deleteIcon, ArrayHelper::merge([
			'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
			'data-method' => 'post',
		], $this->deleteOptions));
	}

	/**
	 * Initializes the default button rendering callback for single button
	 * @param string $name Button name as it's written in template
	 * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
	 * @param array $additionalOptions Array of additional options
	 * @since 2.0.11
	 */
	protected function initDefaultButton($name, $iconName, $additionalOptions = [])
	{
		if (!isset($this->buttons[$name]) && strpos($this->template, '{'.$name.'}') !== false) {
			$this->buttons[$name] = function($url, $model, $key) use ($name, $iconName, $additionalOptions) {
				switch ($name) {
					case 'view':
						$title = Yii::t('yii', 'View');
						break;
					case 'update':
						$title = Yii::t('yii', 'Update');
						break;
					case 'delete':
						$title = Yii::t('yii', 'Delete');
						break;
					default:
						$title = ucfirst($name);
				}
				$options = array_merge([
					'title' => $title,
					'aria-label' => $title,
					'data-pjax' => '0',
				], $this->buttonOptions, $additionalOptions);
				$icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
				return Html::a($icon, $url, $options);
			};
		}
	}

	/**
	 * Renders the header cell.
	 */
	public function renderHeaderCell()
	{
		$_headerOptions = ['class' => 'action-column'];
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
