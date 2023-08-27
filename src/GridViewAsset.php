<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace xpbl4\grid;

/**
 * This asset bundle provides the javascript files for the [[GridView]] widget.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridViewAsset extends \yii\grid\GridViewAsset
{
    public $sourcePath = '@xpbl4/grid/assets';

	public $css = [
		'css/yii.gridView.css',
	];

    public $js = [
        'js/yii.gridView.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
