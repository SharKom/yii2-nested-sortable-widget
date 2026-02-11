<?php

/**
 * @inheritdoc
 */

namespace sharkom\yii2nestedSortable;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Sortable;
use yii\web\JsExpression;
use const PHP_EOL;

/**
 * @inheritdoc
 */
class NestedSortable extends Sortable
{

    public $contentAttribute = 'title';
    public $itemsAttribute = 'pages';
    public $descriptionAttribute = "type,url";
    public $url = [];
    public $handleOptions = [];
    public $pjaxSuccessId;
    public $updateAction="";
    public $deleteAction="";
    public $options = [
      "id"=>"NestedSortableSet"
    ];
    public $clientOptions = [
        'forcePlaceholderSize' => true,
        'handle'               => 'div',
        'listType'             => 'ul',
        'helper'               => 'clone',
        'items'                => 'li',
        'opacity'              => .6,
        'placeholder'          => 'placeholder',
        'revert'               => 250,
        'tabSize'              => 25,
        'tolerance'            => 'pointer',
        'toleranceElement'     => '> div',
        'maxLevels'            => 4,
        'isTree'               => true,
        'expandOnHover'        => 700,
        'startCollapsed'       => false,
    ];

    public function run()
    {

        if (isset($this->options['tag'])) {
            $this->clientOptions['listType'] = ArrayHelper::remove($this->options, 'tag', 'ul');
        }
        if (isset($this->handleOptions['tag'])) {
            $this->clientOptions['handle'] = ArrayHelper::remove($this->handleOptions, 'tag', 'div');
        }
        if (isset($this->itemOptions['tag'])) {
            $this->clientOptions['items'] = ArrayHelper::remove($this->itemOptions, 'tag', 'li');
        }
        if (!isset($this->clientEvents['update'])) {
            $this->clientEvents['update'] = new JsExpression("function(){
                $.ajax({
                        method: 'POST',
                        url: '" . Url::to($this->url) . "',
                        data: $('#" . $this->options['id'] . "').nestedSortable('serialize'),
                        dataType: 'json'
                    }).done(function( data ) {
                        console.log(data) 
                        ".(($this->pjaxSuccessId)?"$.pjax.reload($('#$this->pjaxSuccessId'),{timeout:false});":'')."
                       // if(data)alert(data);
                    });
					
				}");
        }
        $this->registerWidget('nestedSortable');
        return $this->renderItemsR($this->items) . PHP_EOL;
    }

    /**
     * Renders sortable items as specified on [[items]].
     * @return string the rendering result.
     * @throws InvalidConfigException.
     */
    public function renderItemsR($models)
    {
        $items = [];
        $items[] = Html::beginTag($this->clientOptions['listType'], $this->options) . PHP_EOL;
        ArrayHelper::remove($this->options, 'id');
        foreach ($models as $model) {

            $pcontent="<span class='small'>";
            $spans=explode(",", $this->descriptionAttribute);
            foreach ($spans as $span) {
                $pcontent.=$model->{$span};
                $pcontent.=" - ";
            }
            $pcontent=substr($pcontent, 0, -3);
            $pcontent."</span>";



            if($this->deleteAction!="") {
                $pcontent.="<span class='pull-right action'>".Html::a('<span class="glyphicon glyphicon-trash"></span>', [$this->deleteAction, 'id' => $model->id], ['data' => ['confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),'method' => 'post',],])."</span> ";
            }

            if($this->updateAction!="") {
                $pcontent.="<span class='pull-right action'>".Html::a('<span class="glyphicon glyphicon-pencil"></span>', [$this->updateAction, 'id'=>$model->id])."</span> ";
            }

            $content = Html::tag($this->clientOptions['handle'], $model->{$this->contentAttribute}.$pcontent, $this->handleOptions);

            if ($model->{$this->itemsAttribute}) {
                \Yii::debug("DEBUG". print_r($model->{$this->itemsAttribute}, true));
                $content .= $this->renderItemsR($model->{$this->itemsAttribute});
            }
            if($model->nestable!="1") {
                $options = ArrayHelper::merge($this->itemOptions, ['id' => 'item-' . $model->id, 'class'=>'mjs-nestedSortable-no-nesting']);
            } else {
                $options = ArrayHelper::merge($this->itemOptions, ['id' => 'item-' . $model->id]);
            }

            $items[] = Html::tag($this->clientOptions['items'], $content, $options);
        }

        $items[] = Html::endTag($this->clientOptions['listType']) . PHP_EOL;
        return implode("\n", $items);
    }

    /**
     * Registers a specific jQuery UI widget asset bundle, initializes it with client options and registers related events
     * @param string $name the name of the jQuery UI widget
     * @param string $id the ID of the widget. If null, it will use the `id` value of [[options]].
     */
    protected function registerWidget($name, $id = null)
    {
        if ($id === null) {
            $id = $this->options['id'];
        }
        NestedSortableAsset::register($this->getView());
        NestedAsset::register($this->getView());
        $this->registerClientEvents($name, $id);
        $this->registerClientOptions($name, $id);
    }

}
