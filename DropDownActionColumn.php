<?php
namespace dowlatow\widgets;

use Yii;
use yii\bootstrap\ButtonDropdown;
use yii\grid\Column;
use yii\helpers\Html;
use yii\helpers\Url;

class DropDownActionColumn extends Column
{
    public $buttonOptions = [];

    public $label = '';

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;

    public $items = [];

    public $linkOptions = [];

    public $btnContainerOptions = [];

    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;

    public function init()
    {
        parent::init();
        $this->initDefaultItems();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultItems()
    {
        if (!$this->items) {
            $this->items = ['view', 'update', 'delete'];
        }

        $this->items = array_map(function ($item) {

            if (is_callable($item)) {
                return $item;
            }

            switch ($item) {
                case 'delete':
                    return function ($model, $index, $key) use ($item) {
                        return [
                            'label'       => Yii::t('yii', 'Delete'),
                            'url'         => $this->createUrl($item, $model, $key, $index),
                            'linkOptions' => array_merge($this->linkOptions, [
                                'data-pjax'    => '0',
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method'  => 'post',
                            ]),
                        ];
                    };
                default:
                    return function ($model, $index, $key) use ($item) {
                        return [
                            'label'       => Yii::t('yii', strtoupper($item)),
                            'url'         => $this->createUrl($item, $model, $key, $index),
                            'linkOptions' => array_merge($this->linkOptions, [
                                'data-pjax' => '0',
                            ]),
                        ];
                    };
            }

        }, $this->items);
    }


    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param $model - data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params    = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }

    protected function generateItems($model, $key, $index)
    {
        return array_map(function ($item) use ($model, $index, $key) {

            return call_user_func($item, $model, $index, $key, $this);

        }, $this->items);
    }

    protected function isLastRow($index)
    {
        $index++;
        return $index >= $this->grid->dataProvider->getPagination()->getPageSize()
        || $index >= $this->grid->dataProvider->getTotalCount();
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $options = $this->buttonOptions;
        if ($this->isLastRow($index)) {
            Html::addCssClass($options, 'dropup');
        }
        return ButtonDropdown::widget([
            'containerOptions' => $options,
            'label'            => $this->label,
            'options'          => $this->buttonOptions,
            'dropdown'         => [
                'options' => [
                    'class'          => 'left-side',
                    'data-placement' => 'auto',
                ],
                'items'   => $this->generateItems($model, $key, $index),
            ],
        ]);
    }
}