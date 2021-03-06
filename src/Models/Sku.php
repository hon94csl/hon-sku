<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:35 下午
 */

namespace Hon\HonSku\Models;


use Hon\HonSku\Contracts\AttrContract;
use Hon\HonSku\Contracts\SkuContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Sku extends Model implements SkuContract
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('hon-sku.table_names.skus'));
        //监听删除
        static::deleting(function (self $sku) {
            //sku删除时移除与属性的关联关系
            $sku->attrs()->detach();
        });
    }

    /**
     * Notes: 关联产品
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:45 下午
     * @return MorphTo
     */
    public function producible(): MorphTo
    {
        return $this->morphTo(config('hon-sku.morph_name'));
    }

    /**
     * Notes: 关联属性
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:48 下午
     * @return BelongsToMany
     */
    public function attrs(): BelongsToMany
    {
        return $this->belongsToMany(
            config('hon-sku.models.Attr'),
            config('hon-sku.table_names.attr_sku')
        )->using(config('hon-sku.models.AttrSku'));
    }

    /**
     * Notes: 同步属性
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:49 下午
     * @param mixed ...$attrs
     * @return mixed|void
     */
    public function syncAttrs(...$attrs)
    {
        $attrs = $this->parseEqualProductAttrs(...$attrs);

        $this->attrs()->sync($attrs->map->getKey());
    }

    /**
     * Notes: 分配属性键值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:50 下午
     * @param mixed ...$attrs
     * @return mixed|void
     */
    public function assignAttrs(...$attrs)
    {
        $attrs = $this->parseEqualProductAttrs(...$attrs);

        $this->attrs()->attach($attrs->map->getKey());
    }

    /**
     * Notes: 删除属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:50 下午
     * @param mixed ...$attrs
     * @return mixed|void
     */
    public function removeAttrs(...$attrs)
    {
        $attrs = $this->parseEqualProductAttrs(...$attrs);

        $attrs->isEmpty()
            ? $this->attrs()->detach()
            : $this->attrs()->detach($attrs);
    }

    /**
     * Notes: 通过属性值组合查询sku实例
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:51 下午
     * @param mixed ...$position
     * @return mixed
     */
    public static function findByPosition(...$position)
    {
        $position = collect($position)
            ->flatten()
            ->map(function ($val) {
                if (is_numeric($val) || is_string($val)) {
                    return $val;
                }

                if ($val instanceof AttrContract) {
                    return $val->getKey();
                }

                throw new \InvalidArgumentException('无效属性值');
            })
            ->unique()
            ->toArray();

        // 子查询
        $skuIdsQuery = AttrSku::select('sku_id')
            ->whereIn(
                'sku_id',
                AttrSku::select('sku_id')->whereIn('attr_id', $position)
            );

        return Sku::query()
            ->whereIn('id', $skuIdsQuery)
            ->withCount('attrs')
            ->group('id')
            ->having('attrs_count', '=', count($position))
            ->first();
    }


    /**
     * Notes: 解析同产品下属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:57 下午
     * @param mixed ...$attrs
     * @return Collection
     */
    protected function parseEqualProductAttrs(...$attrs): Collection
    {
        return collect($attrs)->flatten()->map(function ($attr) {
            $attr = $this->getStoredAttr($attr);

            if (!$this->attrIsFormEqualProduct($attr)) {
                throw new \InvalidArgumentException('无效属性值');
            }

            return $attr;
        });
    }

    /**
     * Notes: 获取属性值实例
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:59 下午
     * @param $attr
     * @return AttrContract
     */
    protected function getStoredAttr($attr): AttrContract
    {
        if (is_numeric($attr)) {
            $attrModel = config('hon-sku.models.Attr');
            $attr = $attrModel::findOrFail($attr);
        }

        if (!$attr instanceof AttrContract) {
            throw new \InvalidArgumentException('无效属性值');
        }

        return $attr;
    }

    /**
     * Notes: 判断属性值是否与sku来自同一产品
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:59 下午
     * @param AttrContract $attr
     * @return bool
     */
    protected function attrIsFormEqualProduct(AttrContract $attr): bool
    {
        return $attr->producible->is($this->producible);
    }
}