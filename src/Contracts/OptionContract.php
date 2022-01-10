<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:14 下午
 */

namespace Hon\HonSku\Contracts;


interface OptionContract
{
    /**
     * Notes: 通过名称查询选项
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 3:15 下午
     * @param string $name
     * @return mixed
     */
    public static function findByName(string $name);
}