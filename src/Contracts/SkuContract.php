<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:16 下午
 */

namespace Hon\HonSku\Contracts;



use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface SkuContract
{
    /**
     * Notes: 获取所属产品
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:28 下午
     * @return MorphTo
     */
    public function producible(): MorphTo;

    /**
     * Notes: 获取属性键值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:29 下午
     * @return BelongsToMany
     */
    public function attrs(): BelongsToMany;


    /**
     * Notes:同步属性键值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:29 下午
     * @param mixed ...$attrs
     * @return mixed
     */
    public function syncAttrs(...$attrs);


    /**
     * Notes:分配属性键值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:29 下午
     * @param mixed ...$attrs
     * @return mixed
     */
    public function assignAttrs(...$attrs);

    /**
     * Notes: 删除属性值
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:29 下午
     * @param mixed ...$attrs
     * @return mixed
     */
    public function removeAttrs(...$attrs);

    /**
     * Notes:通过属性值组合查询sku实例
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:30 下午
     * @param mixed ...$position
     * @return mixed
     */
    public static function findByPosition(...$position);
}