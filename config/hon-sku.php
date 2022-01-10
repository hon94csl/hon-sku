<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 2:25 下午
 */
return [
    /*
     * 表名映射
     * 该映射的目的是支持用户自定义表和拓展不同业务逻辑实现
     */
    'table_names' => [
        /*
         * sku表,金额，库存等信息
         */
        'skus' => 'skus',

        /*
         * 选项表，单独存放sku属性选项值，如"颜色","尺寸"
         */
        'options' => 'options',

        /*
         * 商品的属性值表，商品同选项下不同值为多个属性值
         */
        'attrs' => 'attrs',

        /*
         * sku与产品属性值之前的多对多关联表，用于确认sku所对应的属性值搭配
         */
        'attr_sku' => 'attr_sku',
    ],

    /*
     * 模型映射
     */
    'models' => [
        /*
         * sku模型，需实现 Hon\HonSku\Contracts\SkuContract
         */
        'Sku' => \Hon\HonSku\Models\SkuM::class,

        /*
         * 选项模型，需实现 Hon\HonSku\Contracts\OptionContract
         */
        'Option' => \Hon\HonSku\Models\OptionM::class,

        /*
         * 属性值模型,需实现 Hon\HonSku\Contracts\AttrContract
         */
        'Attr' => \Hon\HonSku\Models\AttrM::class,

        /*
         * 属性与SKU多对多中间模型
         */
        'AttrSku' => \Hon\HonSku\Models\AttrSkuM::class,
    ],

    /*
     * 商品多态关联名称
     */
    'morph_name' => 'producible'
];
