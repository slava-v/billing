<?php

declare(strict_types=1);

namespace Billie\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    private ?KernelBrowser $client;

    private ?Generator $faker;

    final protected function getClient(): KernelBrowser
    {
        if (!isset($this->client) || !$this->client instanceof KernelBrowser) {
            $this->client = static::$booted
                ? static::$kernel->getContainer()->get('test.client')
                : static::createClient();
            assert($this->client instanceof KernelBrowser);
        }
        return $this->client;
    }

    /**
     * @template T of object
     * @param class-string<T> $serviceFqcn
     * @return T
     */
    final protected function getService(string $serviceFqcn)
    {
        $serviceInstance = $this->getContainer()->get($serviceFqcn);
        assert($serviceInstance instanceof $serviceFqcn);
        return $serviceInstance;
    }

    /**
     * Returns instance of faker.
     * @todo Can be localized by adding the locale as argument
     */
    final protected function getFaker(string $locale = 'de_DE'): Generator
    {
        if (!isset($this->faker)) {
            $this->faker = Factory::create($locale);
        }

        return $this->faker;
    }

    private function isSchemaLoaded(): bool
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        return count($connection->createSchemaManager()->listTableNames()) > 0;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService(EntityManagerInterface::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!$this->isSchemaLoaded()) {
            throw new \Exception('No database loaded yet. Run first bin/console doctrine:fixtures:load --env=test');
        }
    }

    protected function tearDown(): void
    {
        $this->client = null;
        parent::tearDown();
    }
}
