<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:38 下午
 */

namespace Hon\HonSku\Traits;


use Hon\HonSku\Contracts\AttrContract;
use Hon\HonSku\Contracts\OptionContract;
use Hon\HonSku\Models\SkuM;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HasSku
{

    /**
     * Notes: 商品属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:08 下午
     * @return MorphMany
     */
    public function attrs(): MorphMany
    {
        return $this->morphMany(config('hon-sku.models.Attr'), config('hon-sku.morph_name'));
    }


    /**
     * Notes:商品sku
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:11 下午
     * @return MorphMany
     */
    public function skus(): MorphMany
    {
        // sku列表，及每条sku对应属性键值列表，价格，库存
        return $this->morphMany(config('hon-sku.models.Sku'), config('hon-sku.morph_name'));
    }

    /**
     * Notes:同步属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:13 下午
     * @param  OptionContract|integer|string $option
     * @param mixed ...$values
     */
    public function syncAttrValues($option, ...$values)
    {
        //选项不存则创建
        $option_id = $this->findOrCreateOption($option)->getKey();
        $values = Arr::flatten($values);

        // 已存在指定values的属性值
        $has = $this->attrs()
            ->where('option_id', $option_id)
            ->whereIn('value', $values)
            ->get();

        $attrClass = config('hon-sku.models.Attr');
        $attrModelKeyName = (new $attrClass)->getKeyName();

        $newAttrs = [];
        foreach ($values as $value) {
            // 不存在的新值写入新增数组
            if (is_null($has->firstWhere('value', $value))) {
                $newAttrs[] = new $attrClass(compact('option_id', 'value'));
            }
        }

        DB::transaction(function () use ($option_id, $attrModelKeyName, $has, $newAttrs) {
            // 删除其它
            $this->attrs()
                ->where('option_id', $option_id)
                ->whereNotIn($attrModelKeyName, $has->pluck($attrModelKeyName))
                ->delete();

            // 保存新增
            $this->attrs()->saveMany($newAttrs);
        });
    }


    /**
     * Notes: 移除商品的某选项的对应属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:14 下午
     * @param $option
     * @return mixed
     */
    public function removeAttrValues($option)
    {
        $option = $this->findOrCreateOption($option);

        return $this->attrs()->where('option_id', $option->getKey())->delete();
    }


    /**
     * Notes: 添加属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:15 下午
     * @param OptionContract|integer|string $option
     * @param mixed ...$values
     * @return iterable
     */
    public function addAttrValues($option, ...$values)
    {
        if (empty($values)) {
            throw new \InvalidArgumentException('属性值不能为空');
        }

        $option = $this->findOrCreateOption($option);

        $values = collect($values)->flatten()->map(function ($value) use ($option) {
            $attribute_class = config('hon-sku.models.Attr');
            $option_id = $option->getKey();
            return new $attribute_class(compact('option_id', 'value'));
        });

        return $this->attrs()->saveMany($values);
    }


    /**
     * Notes: 通过属性值组合更新或创建sku
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:17 下午
     * @param array $position
     * @param array $payload
     * @return SkuM|Model|mixed|object|null
     */
    public function syncSkuWithAttrs(array $position, array $payload)
    {
        // 验证属性值不能为空
        if (empty($position)) {
            throw new \InvalidArgumentException('配置不能为空');
        }

        // 解析属性值为属性值id数组
        $position = array_map(function ($point) {
            if (is_numeric($point) || is_string($point)) {
                return $point;
            }

            if ($point instanceof AttrContract) {
                return $point->getKey();
            }

            throw new \InvalidArgumentException("无效属性值");
        }, $position);

        // 验证属性值重复
        if (count($position) !== count(array_unique($position))) {
            throw new \InvalidArgumentException('重复属性值');
        }

        $attrModel = config('hon-sku.models.Attr');
        // 各属性值的选项id
        $option_ids = $this->attrs()
            ->whereIn((new $attrModel)->getKeyName(), $position)
            ->pluck('option_id')
            ->toArray();

        // 验证此商品下存在此属性值
        if (count($position) !== count($option_ids)) {
            throw new \InvalidArgumentException('该选项下的属性值不存在');
        }

        // 验证同一SKU下的属性值的选项名称不重复
        if (count($option_ids) !== count(array_unique($option_ids))) {
            throw  new \InvalidArgumentException('同一SKU下的属性值的选项名称重复');
        }

        $sku = SkuM::findByPosition($position);

        $sku = $this->skus()->create($payload);
        $sku->attrs()->sync($position);


        return $sku;
    }

    /**
     * Notes: 查询选项
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 5:22 下午
     * @param OptionContract|integer|string $option
     * @return OptionContract
     */
    protected function findOrCreateOption($option): OptionContract
    {
        $optionModel = config('hon-sku.models.Option');

        if (is_numeric($option)) {
            $option = $optionModel::findOrFail($option);
        }

        if (is_string($option)) {
            $option = $optionModel::firstOrCreate(['name' => $option]);
        }

        if (! ($option instanceof OptionContract)) {
            throw (new ModelNotFoundException)->setModel($optionModel);
        }

        return $option;
    }
}