<?php
/**
 * @author: Sergey Mashkov (serge@asse.com)
 * Date: 4/24/23 10:59 AM
 * Project: asse-db-template
 */

namespace xpbl4\grid;

class PriorityColumnAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@xpbl4/grid/assets';

	public $js = [
		'js/jquery-sortable-widget.js',
		'js/Sortable.js'
	];

	public $css = [
		'css/sortable-widget.css',
	];
}