<?php declare(strict_types=1);

namespace bPack\Protocol;

trait HookTrait {
    protected array $hooks = [];

    abstract public function getHooks():array;

    // registerHook return hook id
    public function registerHook(string $hookIdentifier, callable $method):int {
        if(!in_array($hookIdentifier, $this->getHooks() ) ) {
            throw new \Exception("["  . getclass($this) . "] Hook {$hookIdentifier} not exists.");
        }

        $this->hooks[$hookIdentifier] ??= [];
        return array_push($this->hooks[$hookIdentifier], $method);
    }

    //
    public function unregisterHook(string $hookIdentifier, int $hookId):bool {
        if(!in_array($hookIdentifier, $this->getHooks() ) ) {
            throw new \Exception("[" . getclass($this) . "] Hook {$hookIdentifier} not exists.");
        }

        if(!isset($this->hooks[$hookIdentifier])) {
            return true; // no hook registereed, then consider done
        }

        unset($this->hooks[$hookIdentifier][$hookId]);

        return true;
    }

    //
    public function runHook(string $hookIdentifier):bool {
        if(!in_array($hookIdentifier, $this->getHooks() ) ) {
            throw new \Exception("[" . getclass($this) . "] Hook {$hookIdentifier} not exists.");
        }

		//  if there's no hook, then assue done.                                                                                                                     
		if(!isset($this->hooks[$hookIdentifier])) {                                                                    
			return true;
		}

        foreach($this->hooks[$hookIdentifier] as $hookfunc) {
            $hookfunc($this);
        }

        return true;
    }
}
