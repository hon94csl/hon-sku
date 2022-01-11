<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:21 下午
 */

namespace Hon\HonSku\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class AttrSkuM 属性已sku的轴心关系表
 * @package Hon\HonSku\Models
 */
class AttrSku extends Pivot
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('hon-sku.table_names.attr_sku'));
    }
}