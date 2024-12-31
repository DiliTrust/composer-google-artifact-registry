<?php

declare(strict_types=1);

namespace DiliTrust\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

final class GoogleArtifactRegistryPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        $configuration = $composer->getConfig()->get('google-artifact-registry') ?? [];
        $repositories = $configuration['repositories'] ?? [];
        foreach ($repositories as $repository) {
            $composer->getRepositoryManager()
                ->addRepository(new GoogleArtifactRegistryRepository($repository, $io));
        }
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}
