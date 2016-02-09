# dropdown-action-column

DropDownActionColumn - виджет для отображения дествий в строке таблицы в виде выпадающего списка

## Установка

1. Загрузить через git: https://github.com/novokshonovev/dropdown-action-column.git
или 
2. Установка через composer 
2.1 Добавить в composer.json проекта:
* репозиторий 
```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/novokshonovev/dropdown-action-column"
        }
    ],
```
* и зависимость
```json
    "require": {
        "dowlatow/dropdown-action-column": "dev-master"
    },
```
2.2 Выполнить установку: ``composer install``

## Пример использования

```php
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns'      => [
            [
                'class'  => 'yii\grid\SerialColumn',
                'header' => '№',
            ],
            [
                'attribute' => 'name',
                'label'     => $searchModel->getAttributeLabel('name'),
            ],
            [
                'attribute' => 'created_at',
                'label'     => $searchModel->getAttributeLabel('created_at'),
                'format'    => 'date',
            ],
            [
                'attribute' => 'ownerName',
                'label'     => $searchModel->getAttributeLabel('ownerName'),
                'content'   => function (Organization $model) {
                    $user = $model->owner;
                    if (!$user) {
                        return null;
                    }
                    return Html::a($user->getName(), ['/user/user/view', 'id' => (string)$user->_id]);
                },
            ],
            [
                'attribute' => 'projectsCount',
                'label'     => $searchModel->getAttributeLabel('projectsCount'),
            ],
            [
                'class'         => DropDownActionColumn::className(),
                'label'         => false,
                'buttonOptions' => [
                    'class' => 'btn-icon h-dots',
                ],
                'items'         => [
                    function (Organization $model, $index, $key, DropDownActionColumn $column) {
                        return [
                            'label' => Yii::t('common', 'Open'),
                            'url'   => ['update', 'id' => (string)$model->_id],
                        ];
                    },
                    function (Organization $model, $index, $key, DropDownActionColumn $column) {
                        return [
                            'label'   => Yii::t('organization', 'Activate'),
                            'url'     => ['activate', 'id' => (string)$model->_id],
                            'visible' => !$model->isActive
                        ];
                    },
                    function (Organization $model, $index, $key, DropDownActionColumn $column) {
                        return [
                            'label'   => Yii::t('organization', 'Deactivate'),
                            'url'     => ['deactivate', 'id' => (string)$model->_id],
                            'visible' => $model->isActive
                        ];
                    },
                    function (Organization $model, $index, $key, DropDownActionColumn $column) {
                        return [
                            'label'       => Yii::t('common', 'Delete'),
                            'url'         => ['delete', 'id' => (string)$model->_id],
                            'linkOptions' => array_merge(['data-method' => 'post',], $column->linkOptions),
                        ];
                    },
                ],
            ]
        ],
    ]); ?>
```