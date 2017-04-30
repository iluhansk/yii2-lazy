# yii2-lazy
=============
implementation pattern design "lazy load" for components of yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist iluhansk/yii2-lazy "*"
```

or add

```
"iluhansk/yii2-lazy": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Add LazyBehavior to target class and define methods corresponding lazy properties:

<pre>
namespace common\components;

use \yii\base\Component;
use iluhansk\renderer\LazyBehavior;

/**
 * @property int $passengersCount Count of passengers in the car
 */
class Car extends Component {

    public function behaviors() {
        return [
            'lazy' => [
                'class' => LazyBehavior::className()
            ],
        ];
    }

    public function lazy_passengersCount() {
        //some logic here, for example:
        return rand(1,4);
    }

}
</pre>

And use lazy property in any place:

<pre>
use common\components\Car;

echo "PassengersCount first call: $o->passengersCount<br>"; //first use lazy property will call lazy_passengersCount method of object
echo "PassengersCount second call: $o->passengersCount<br>"; //this will return stored value (and not call lazy_passengersCount method again)
</pre>
