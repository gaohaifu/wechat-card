<?php
namespace addons\posters\lib;

abstract class Collection
{
    /**
     * The collection data.
     *
     * @var array
     */
    protected $items = [];

    /**
     * 只读属性 外部不能修改
     *
     * @var array
     */
    protected $readonly = [];

    /**
     * set data.
     *
     * @param mixed $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get a data by key.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    public function __set(string $key, $value)
    {
        return $this->set($key, $value);
    }

    public function __call(string $key, $value)
    {
        return count($value) > 0 ? $this->set($key, $value[0]) : $this->get($key);
    }

    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function has(string $key): bool
    {
        return !is_null($this->get($key));
    }

    public function get(string $key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        if (isset($this->items[$key]) && !in_array($key, $this->readonly)) {
            $method = "set".ucfirst(strtolower($key))."Attr";
            if (method_exists($this, $method)){
                $this->$method($value);
            }else{
                $this->items[$key] = $value;
            }
        }
        return $this;
    }

}
