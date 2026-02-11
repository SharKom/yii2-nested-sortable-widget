<?php

namespace sharkom\yii2nestedSortable;

use yii\web\AssetBundle;

class NestedAsset extends AssetBundle
{
    public $sourcePath = '@vendor/sharkom/yii2-nested-sortable-widget/assets/';
    public $css = [
        'css/nestedSortable.css',
    ];
    public $depends = [
        'yii\jui\JuiAsset',
    ];

    public function init()
    {
        parent::init();
        $this->publishOptions['forceCopy'] = true;
    }

}