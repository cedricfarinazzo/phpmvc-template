<?php

class CacheManager
{

    const CACHEEXPIRE = 900;
    protected $cache_folder = ROOT_PATH . '/.cache/';
    protected $page;
    protected $parametre;

    public function __construct($page, $param = NULL)
    {
        if (!file_exists($this->cache_folder)) {
            @mkdir($this->cache_folder);
        }
        $this->page = $page;
        $this->parametre = $param;
    }

    public function readcache()
    {
        if ($this->on_cache()) {
            if (!empty($this->parametre)) {
                return @file_get_contents($this->cache_folder . $this->page . '/' . $this->parametre . '.cache');
            } else {
                return @file_get_contents($this->cache_folder . $this->page . '/' . $this->page . '.cache');
            }
        }
        return false;
    }

    public function on_cache()
    {
        if (file_exists($this->cache_folder . $this->page . '/') && is_dir($this->cache_folder . $this->page . '/')) {
            if (!empty($this->parametre)) {
                if (file_exists($this->cache_folder . $this->page . '/' . $this->parametre . '.cache')) {
                    return filemtime($this->cache_folder . $this->page . '/' . $this->parametre . '.cache') > time() - self::CACHEEXPIRE;
                }
                return false;
            } else {
                if (file_exists($this->cache_folder . $this->page . '/' . $this->page . '.cache')) {
                    return filemtime($this->cache_folder . $this->page . '/' . $this->page . '.cache') > time() - self::CACHEEXPIRE;
                }
                return false;
            }
        }
        return false;
    }

    public function createcache($content)
    {
        if (!file_exists($this->cache_folder . $this->page . '/') || !is_dir($this->cache_folder . $this->page . '/')) {
            mkdir($this->cache_folder . $this->page . '/');
        }
        if (!empty($this->parametre)) {
            return @file_put_contents($this->cache_folder . $this->page . '/' . $this->parametre . '.cache', $content);
        } else {
            return @file_put_contents($this->cache_folder . $this->page . '/' . $this->page . '.cache', $content);
        }
    }
}