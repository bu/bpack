<?php declare(strict_types=1);
namespace bPack;

class Session implements Protocol\Session, Protocol\Module {
    protected bool $initialized = false;
    protected ?string $sessionId = null;
    protected ?array $sessionData = null;
    protected array $cookieOptions = [];

    // module init
    public function __construct(Protocol\SessionStorage $storage) {
        $this->storage = $storage;

        $this->cookieOptions = [
            "httponly" => true,
            "expires" => time() + 3600
        ];
    }

    // for Protocol\Module
    public function getIdentitifer():string {
        return "session";
    }

    public function setApplication(Foundation $app): void {
        $this->app = $app;
        $app->config->required(["SESSION_SECRET"]);
    }

    // actual session code
    public function getSessionName(): string {
        return $_ENV["SESSION_NAME"] ?? "bPack_Session";
    }

    public function start(Protocol\Request $req, Protocol\Response $res):Session {
        // try to get session id from cookie
        // if not we should create one
        $this->sessionId = $req->cookie( $this->getSessionName(), null) ?? $this->getSessionId();
        $this->initialized = true;
        $this->sync();

        // write cookie about session id
        $sess = $this;

        $res->registerHook("beforeSend", function(Response &$res) use ($sess) {
            $sess->sync();
            setcookie($this->getSessionName(), $this->sessionId, $this->cookieOptions);
        });

        return $this;
    }

    public function set(string $key, $value):Session {
        // if we have no session data, we should sync it with storage
        $this->sessionData[$key] = $value;
        return $this;
    }

    public function get(string $key, $default_value = null) {
        // if we have no session data, we should sync it with storage
        if($this->sessionData === null) {
            $this->sync();
        }

        return $this->sessionData[$key] ?? $default_value;
    }

    public function delete(string $key):bool {
        // if we have no session data, we should sync it with storage
        if($this->sessionData === null) {
            $this->sync();
        }

		// sync()  will remove all null value property
        $this->sessionData[$key] = null;

        return true;
    }

    public function flush(string $key) {
        // if we have no session data, we should sync it with storage
        if($this->sessionData === null) {
            $this->sync();
		}

		$value = $this->get($key);
		$this->delete($key);

        return $value;
    }

    public function destroy():bool {
        // delete entire session
        $this->storage->destroy($this->sessionId);
        $this->sessionData = null;

        //
        $this->sessionId = $this->getSessionId(true);
        $this->sync();

        return true;
    }

    // internal methods

    protected function sync():Session {
        // here try to read from storage and temporaily store at Cache
        if($this->initialized == false) {
            return $this;
        }

		// 1 read from storage
        $storedValue = $this->storage->read($this->sessionId);

		// use new values to overwrite stored value
		$newValues = array_merge($storedValue, $this->sessionData ?? []);

		// clear null values
		$newValues = array_filter($newValues);

		// then write back to presisent medium
		$this->storage->write($this->sessionId, $newValues);

		// replace current values with
		$this->sessionData = $newValues;

		return $this;
    }

    public function getSessionId(bool $force = false):string {
        if ($this->sessionId !== null && !$force) {
            return $this->sessionId;
        }

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
