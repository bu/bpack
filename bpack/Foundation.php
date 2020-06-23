<?php declare(strict_types=1);
namespace bPack;

class Foundation
{
    public \Dotenv\Dotenv $config;
    public string $appDir;

    final function rootpath($path = null):string {
        if(is_null($path) ) {
            return $this->appDir;
        }

        $path ??= "";
        return $this->appDir . "/" . $path;
    }

    public function __construct(string $appDir)
    {
        // push
        $this->appDir = $appDir;

        // load environment variables
        $this->config = \Dotenv\Dotenv::createImmutable($appDir);
        $this->config->load();
        $this->config->required(["ENV"]);

        // timezone setting
        date_default_timezone_set($_ENV["TIMEZONE"] ?? "UTC");
    
    }

    public function isDevMode(): bool
    {
        return (strtoupper($_ENV["ENV"]) == "DEV");
    }

    public function terminate():void {
        if (isset($_SERVER) && isset($_SERVER["GATEWAY_INTERFACE"])) {
            fastcgi_finish_request();
        }

        exit;
    }

    // service part
    public function load(Protocol\Module $mod):bool {
        if(isset($this->{$mod->getIdentitifer()})) {
            return true;
        }

        $this->{$mod->getIdentitifer()} = $mod;
        $mod->setApplication($this);

        return true;
    }

    public function unload(Protocol\Module $mod):bool {
        unset($this->{$mod->getIdentitifer()});

        return true;
    }

    // request undefined variables
    public function __get(string $key) {
        throw new \Exception("[Foundation] Request undefined property ({$key}), maybe you haven't loaded the modules?");
    }
}
