<?php

namespace ShopwareCli\Services;

use ShopwareCli\Config;
use ShopwareCli\Services\Repositories\RepositoryInterface;

/**
 * Class PluginProvider
 * @package ShopwareCli\Plugin
 */
class PluginProvider
{
    protected $repositories = array();
    protected $sortBy;
    /**
     * @var \ShopwareCli\Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->sortBy = $config['general']['sortBy'];
    }

    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Sort a given array of plugins by the configured properties
     *
     * @param $plugins
     * @return mixed
     */
    protected function sortPlugins($plugins)
    {
        switch ($this->sortBy) {
            case 'repository':
                usort($plugins, function ($a, $b) {
                    return $a->repository . $a->name > $b->repository . $b->name;
                });
                break;
            case 'module':
                usort($plugins, function ($a, $b) {
                    return $a->module . $a->name > $b->module . $b->name;
                });
                break;
            default:
                usort($plugins, function ($a, $b) {
                    return $a->name > $b->name;
                });
                break;
        }

        return $plugins;
    }

    /**
     * Query all plugin repositories for plugins named like $name
     *
     * @param $name     string  Name to search for
     * @param $exact    boolean Whether to search for exact match or not
     * @return mixed
     */
    public function getPluginByName($name, $exact = false)
    {
        $result = array();

        /** @var $repo RepositoryInterface */
        foreach ($this->repositories as $repo) {
            $result = array_merge($result, $repo->getPluginByName($name, $exact));
        }

        return $this->sortPlugins($result);
    }

    /**
     * Query all plugin repositories for all available plugins
     *
     * @return mixed
     */
    public function getPlugins()
    {
        $result = array();

        /** @var $repo RepositoryInterface */
        foreach ($this->repositories as $repo) {
            $result = array_merge($result, $repo->getPlugins());
        }

        return $this->sortPlugins($result);
    }
}