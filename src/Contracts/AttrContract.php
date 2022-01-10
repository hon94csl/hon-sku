<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:10 下午
 */

namespace Hon\HonSku\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface AttrContract
{
    /**
     * Notes: 获取所属选项
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:25 下午
     * @return BelongsTo
     */
    public function option(): BelongsTo;


    /**
     * Notes: 获取所属产品
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:26 下午
     * @return MorphTo
     */
    public function producible(): MorphTo;

    /**
     * Notes:获取使用此键值的sku
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:26 下午
     * @return BelongsToMany
     */
    public function skus(): BelongsToMany;
}