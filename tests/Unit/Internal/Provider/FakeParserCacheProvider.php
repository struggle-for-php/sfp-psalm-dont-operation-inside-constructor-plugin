<?php


namespace SfpTest\Psalm\DontOperationInsideConstructor\Unit\Internal\Provider;

use Psalm\Config;

/**
 * borrowed from psalm
 */
class FakeParserCacheProvider extends \Psalm\Internal\Provider\ParserCacheProvider
{
    public function __construct(Config $config, bool $use_file_cache = false)
    {
        parent::__construct($config, false);
    }

    public function loadStatementsFromCache(string $file_path, int $file_modified_time, string $file_content_hash) : ?array
    {
        return null;
    }

    public function loadExistingStatementsFromCache(string $file_cache_key) : ?array
    {
        return null;
    }

    public function saveStatementsToCache(string $file_cache_key, string $file_content_hash, array $stmts, bool $touch_only) : void
    {
    }

    public function loadExistingFileContentsFromCache(string $file_cache_key) : ?string
    {
        return null;
    }

    public function cacheFileContents(string $file_cache_key, string $file_contents) : void
    {
    }
}
