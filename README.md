# hon-sku
laravel SKU 扩展
### 设计
- 通过 skus，options，attrs和attr_sku四个表存储sku库存管理模块数据
    - 选项     
        商品属性值的键名，如`颜色`,`尺寸` 等
    - 属性值   
        商品某选项对应的值，同一商品同一选项下可有多个属性值
    - SKU   
        库存控制的最小可用单位，可通过不同的属性值组合来搭配不同金额，不同库存的SKU
- 模块数据表操作实现基于Laravel Eloquent 模型关联
- SKU扩展服务以Provider方式使用
### #安装

**引入**

```bash
composer require hon/hon-sku
```

**发布迁移文件**

```bash
php artisan vendor:publish --tag=hon-sku-migrations
```

**运行迁移**

```bash
php artisan migrate
```

**如果需要发布配置文件，请运行以下命令:**

```bash
php artisan vendor:publish --tag=hon-sku-config
```

### #使用

**在商品模型中引入`Hon\HonSku\Traits\HasSku`Trait**

```php
use Illuminate\Database\Eloquent\Model;
use Tmaic\TmaicSku\Traits\HasSku;

class ProductM extends Model
{
    use HasSku;
}
```

---

**选项新增**

```php
use Hon\HonSku\Models\Option;
Option::create(['name' => '尺寸']);
```

**选项删除**

```php
$option->delete();
```

---

**获取商品属性值**

```php
$poduct->attrs()->get();
$poduct->attrs;
```

**新增商品属性值**

```php
$product->addAttrValues($option, ['S', 'M', 'L']);
$product->addAttrValues('包装', ['一包(2.5kg)', '一件(25kg)', '一车(250kg)']);
```

**同步商品属性值**

```php
$product->syncAttrValues($option, ['红色', '白色']);
```

**移除某选项下的所有属性值**

```php
$product->removeAttrValues($option);
```

参数说明:
```php
addAttrValues($option, ...$value)
syncAttrValues($option, ...$value)
removeAttrValues($option)
```
> 1. `$option` 属性实例/属性ID/属性名称
> 2. `$value` 属性值数组 每一项将会创建或同步属性值

---

**创建(同步)SKU**

> 如果属性值存在，则更新SKU，否则创建SKU     
> sku的属性组合是建立在产品基础属性值之上的，分配sku属性值组合前请添加产品属性值

```php
$product->syncSkuWithAttrs([$attr1, $attr2, $attr3], ['amount' => 5000, 'stock' => 100]);
```
`syncSkuWithAttrs`参数说明:
> 1. `$position` 属性值组合数组,每项类型为:`属性值实例`/`属性值ID`
> 2. `$payload` SKU数据，如`金额`,`库存`等

**获取SKU**

```php
use Hon\HonSku\Models\Sku;
// 通过属性值组合获取sku
$sku = Sku::findByPosition($attr1, $attr2);
// 获取产品sku实例
$product->skus()->get();
```

**删除SKU**

```php
$sku->delete();
$product->skus()->delete();
```

**通过属性值组合获取SKU**

```php
use Hon\HonSku\Models\Sku;
Sku::findByPosition([$attr1, $attr2, $attr3])
```

**调整SKU的属性值组合**

```php
// 增加属性值组合
$sku->assignAttrs([$attr1, $attr2])
// 同步属性值组合
$sku->syncAttrs([$attr1, $attr2])
// 移除属性值组合
$sku->removeAttrs([$attr1, $attr2])
```

---

**完整示例**
```php
// 创建产品
$product = Proudcts::create(['productName' => '75-85云皮腿肉']);

// 准备作为sku属性
$unit = $product->addAttrValues('单位', ['包', '件']);
$weight  = $product->addAttrValues('重量', ['250g', '40包']);

// 获取属性值实例
$bag = $unit->firstWhere('value', '包');
$boxful = $unit->firstWhere('value', '箱');
$bagWeight = $weight->firstWhere('value', '250g');
$boxfulWeight = $weight->firstWhere('value', '40包');

// 组合属性值，建立sku
$product->syncSkuWithAttrs([$unit, $bag], ['amount' => 12699, 'stock' => 100]);
$product->syncSkuWithAttrs([$boxful, $boxfulWeight], ['amount' => 12699, 'stock' => 100]);

// 获取商品及商品SKU数据
$product = $product->load('skus.attrs.option');
```