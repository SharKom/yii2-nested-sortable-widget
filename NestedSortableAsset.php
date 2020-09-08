<?php

/**
 * @inheritdoc
 */

namespace sharkom\yii2nestedSortable;

use yii\web\AssetBundle;

/**
 * @inheritdoc
 */
class NestedSortableAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-nested-sortable/';
    public $js = [
        'jquery.mjs.nestedSortable.js',
    ];
     public $depends = [
        'yii\jui\JuiAsset',
    ];

}
