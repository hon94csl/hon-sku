<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 2:55 下午
 */

namespace Hon\HonSku\Models;


use Hon\HonSku\Contracts\AttrContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attr extends Model implements AttrContract
{
    protected  $guarded = [ 'id' ];

    /**
     * AttrM constructor. 设置表
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('hon-sku.table_names.attrs'));
    }

    /**
     * Notes: 获取所属选项 实体方法
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:33 下午
     * @return BelongsTo
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(config('hon-sku.models.Option'));
    }

    /**
     * Notes: 获取所属产品 实体方法
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:35 下午
     * @return MorphTo
     */
    public function producible(): MorphTo
    {
        return $this->morphTo(config('hon-sku.morph_name'));
    }

    /**
     * Notes: 获取使用此键值的sku 实体方法
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:35 下午
     * @return BelongsToMany
     */
    public function skus(): BelongsToMany
    {
        return $this->belongsToMany(
            config('hon-sku.models.Sku'),
            config('hon-sku.table_names.attr_sku')
        )->using(config('hon-sku.models.AttrSku'));
    }
}