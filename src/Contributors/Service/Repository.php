<?php

namespace App\Contributors\Service;

use App\Contributors\Model\Contributor;
use Github\Client;
use Github\ResultPager;

class Repository
{
    const REDIS_KEY_CONTRIBUTORS = 'CONTRIBUTORS';

    protected $client;
    protected $redis;

    public function __construct(Client $client, \Redis $redis)
    {
        $this->client = $client;
        $this->redis = $redis;
    }

    /**
     * @return Contributor[]
     */
    public function all(): array
    {
        try {
            $contributorsByRedis = $this->redis->get(self::REDIS_KEY_CONTRIBUTORS);

            if (empty($contributorsByRedis)) {
                $this->update();
                return $this->all();
            }

            return json_decode($contributorsByRedis, true);

        } catch (\Exception $e) {
            return $this->getContributors();
        }
    }

    /**
     * @return int
     */
    public function update(): int
    {
        $contributors = $this->getContributors();
        $this->redis->set(self::REDIS_KEY_CONTRIBUTORS, json_encode($contributors));

        return \count($contributors);
    }

    /**
     * @return Contributor[]
     */
    private function getContributors(): array
    {
        $contributors = [];
        $results = $this->getContributorsByGithub('PUGX', 'badge-poser');
        foreach ($results as $result) {
            $contributors[$result['login']] = Contributor::create($result['login'], $result['html_url'], $result['avatar_url']);
        }
        $results = $this->getContributorsByGithub('badges', 'poser');
        foreach ($results as $result) {
            $contributors[$result['login']] = Contributor::create($result['login'], $result['html_url'], $result['avatar_url']);
        }

        return $contributors;
    }

    /**
     * @param $username
     * @param $repoName
     * @return array|mixed
     */
    private function getContributorsByGithub($username, $repoName): array
    {
        try {
            $repoApi = $this->client->api('repo');
            $paginator = new ResultPager($this->client);
            $parameters = [$username, $repoName];
            $results = $paginator->fetchAll(
                $repoApi,
                'contributors',
                $parameters
            );
        } catch (\Exception $e) {
            return [];
        }

        return $results;
    }
}
