<?php

namespace BehaviorPacks\loaders;

use BehaviorPacks\behaviorpacks\BehaviorPack;
use BehaviorPacks\behaviorpacks\version\BehaviorVersion;
use BehaviorPacks\utils\PathScanner;
use InvalidArgumentException;
use pocketmine\utils\Config;

class RecipesLoader
{
    public function __construct(protected BehaviorPack $behaviorPack, protected string $path)
    {
        $this->load();
    }

    /**
     * @return void
     */
    public function load(): void
    {
        $files = PathScanner::scanDirectoryToConfig($this->path);
        foreach ($files as $config) {
            $this->parse($config);
        }
    }

    /**
     * @param Config $config
     * @return void
     */
    public function parse(Config $config): void
    {
        $version = $config->get("format_version", null);
        if(!is_string($version)) throw new InvalidArgumentException("Invalid format version ($version)");

        if(!isset(BehaviorVersion::VERSION[$version])) throw new InvalidArgumentException("Invalid version ($version)");

        /** @type BehaviorVersion $behaviorVersion */
        $behaviorVersion = new (BehaviorVersion::VERSION[$version]());
        $this->behaviorPack->addVersion($behaviorVersion);
        $behaviorVersion->parseRecipe($config);
    }
}
