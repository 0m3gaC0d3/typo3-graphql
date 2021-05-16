<?php

/**
 * This file is part of the "typo3-graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Registry;

use InvalidArgumentException;

abstract class AbstractRegistry
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $item
     */
    public function add($item, string $key): void
    {
        if (!$item instanceof $this->type) {
            throw new InvalidArgumentException("The given item is not of configured type \"$this->type\"");
        }
        if (array_key_exists($key, $this->items)) {
            throw new InvalidArgumentException("The given key \"$key\" already is present");
        }
        $this->items[$key] = $item;
    }

    public function remove(string $key): void
    {
        if (!array_key_exists($key, $this->items)) {
            throw new InvalidArgumentException("The given key \"$key\" does not exist");
        }
        unset($this->items[$key]);
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        if (!array_key_exists($key, $this->items)) {
            throw new InvalidArgumentException("The given key \"$key\" does not exist");
        }

        return $this->items[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
