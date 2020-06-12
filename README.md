# Yii2 GridView widget.

[![Latest Version](https://img.shields.io/github/tag/Xpbl4/yii2-grid.svg?style=flat-square&label=release)](https://github.com/Xpbl4/yii2-grid/releases)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/Xpbl4/yii2-grid.svg?style=flat-square)](https://packagist.org/packages/Xpbl4/yii2-grid)

GridView widget extending features of Yii 2 framework widget.

## Install

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xpbl4/yii2-grid "*"
```

or add

```
"xpbl4/yii2-grid": "*"
```

to the require section of your `composer.json` file.

## Usage

Once the extension is installed, simply use it in your code by:

```php
use xpbl4\grid\GridView;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        'created_at:datetime',
        // ...
    ],
]);
```

## GridView

The following functionalities have been added/enhanced:

### Table Filter (Enhanced)
 - Filter/Header position
    - [[FILTER_POS_HEADER]]: the filters will be replace of each column's header cell.
 - Filter error placement
	- [[ERROR_POS_FILTER]]: the errors will be displayed right below each column's filter input.
	- [[ERROR_POS_SUMMARY]]: the errors will be displayed in the filter model {errors} section. See [[renderErrors()]].
	- [[ERROR_POS_TOOLTIP]]: the errors will be displayed in tooltip of each column's filter input.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Serge Mashkov](https://github.com/Xpbl4)
- [All Contributors](../../contributors)

## License

**yii2-grid** is released under the BSD-3-Clause License. Please see [License File](LICENSE.md) for more information.
