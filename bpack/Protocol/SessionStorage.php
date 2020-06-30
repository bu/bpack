<?php declare(strict_types=1);
namespace bPack\Protocol;

interface SessionStorage {
	public function read(string $sessionId):array;
	public function write(string $sessionId, array $data):bool;
	public function destroy(string $sessionId):bool;
}
