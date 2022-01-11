<?php
/**
 * Created by PhpStorm
 * User: hon(陈烁临) qq: 2275604210
 * Date: 2022/1/10
 * Time: 3:24 下午
 */

namespace Hon\HonSku\Models;


use Hon\HonSku\Contracts\OptionContract;
use Illuminate\Database\Eloquent\Model;

class Option extends Model implements OptionContract
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('hon-sku.table_names.options'));
    }

    /**
     * Notes:
     * User: hon(陈烁临) qq: 2275604210
     * Date: 2022/1/10
     * Time: 4:44 下午
     * @param string $name
     * @return mixed
     */
    public static function findByName(string $name)
    {
        return static::where('name',$name)->first();
    }
}