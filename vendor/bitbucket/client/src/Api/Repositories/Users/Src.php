<?php

declare(strict_types=1);

/*
 * This file is part of Bitbucket API Client.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitbucket\Api\Repositories\Users;

/**
 * The src api class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Src extends AbstractUsersApi
{
    /**
     * @param array $params
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function list(array $params = [])
    {
        $path = $this->buildSrcPath();

        return $this->get($path, $params);
    }

    /**
     * @param array $params
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function create(array $params = [])
    {
        $path = $this->buildSrcPath();

        return $this->post($path, $params);
    }

    /**
     * @param string $commit
     * @param string $path
     * @param array  $params
     *
     * @throws \Http\Client\Exception
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function download(string $commit, string $path, array $params = [])
    {
        $path = $this->buildSrcPath($commit, ...explode('/', $path));

        return $this->pureGet($path, $params, ['Accept' => '*/*'])->getBody();
    }

    /**
     * Build the src path from the given parts.
     *
     * @param string[] $parts
     *
     * @throws \Bitbucket\Exception\InvalidArgumentException
     *
     * @return string
     */
    protected function buildSrcPath(string ...$parts)
    {
        return static::buildPath('repositories', $this->username, $this->repo, 'src', ...$parts);
    }
}
