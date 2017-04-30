<?php

namespace iluhansk\lazy;

use Yii;

/**
 * Class provides lazy load properties to yii2 components
 *
 * @author Ilya Perfilyev <ilya_perfi@mail.ru>
 */
class LazyBehavior extends \yii\base\Behavior {

    /**
     *
     * @var string prefix of method corresponding lazy property
     */
    public $prefix = "lazy_";

    /**
     *
     * @var callable Callback method takes parameter name (lazy property name) and return callback corresponding lazy property
     */
    public $methodCallback;
    protected $values = array();

    protected function getDefaultCallback($name) {
        return [$this->owner, "{$this->prefix}{$name}"];
    }

    protected function getLazyCallback($name) {
        if (is_callable($this->methodCallback)) {
            $callback = call_user_func($this->methodCallback, $name);
            if (is_callable($callback)) {
                return $callback;
            }
        }
        //проверяем колбэк по умолчанию
        $defaultCallback = $this->getDefaultCallback($name);
        return is_callable($defaultCallback) ? $defaultCallback : false;
    }

    protected function lazyGet($name) {
        if ($callback = $this->getLazyCallback($name)) {
            return $this->values[$name] = call_user_func($callback);
        }
    }

    public function __get($name) {
        if (!isset($this->values[$name])) {
            $res = $this->lazyGet($name);
            if (isset($res)) {
                return $res;
            }
        } else {
            return $this->values[$name];
        }
        return $this->values[$name] = parent::__get($name);
    }

    public function canGetProperty($name) {
        if (isset($this->values[$name]) || $this->getLazyCallback($name)) {
            return true;
        }
        return parent::canGetProperty($name);
    }

    /**
     * clear the value of lazy property $name, so next time calling this lazy property will call corresponding method (callback)
     * @param string $name
     * @return \yii\base\Behavior
     */
    public function lazyClear($name) {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
        }
        return $this->owner;
    }

    /**
     * force call corresponging method(callback) and recalc lazy property
     * @param string $name
     * @return mixed
     */
    public function lazyForce($name) {
        $this->lazyClear($name);
        return $this->lazyGet($name);
    }

}
