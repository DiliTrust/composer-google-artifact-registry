<?php

namespace DiliTrust\Composer;

use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Repository\ArrayRepository;

class GoogleArtifactRegistryRepository extends ArrayRepository
{
    private readonly string $project;
    private readonly string $location;
    private readonly string $repository;

    public function __construct(
        string $repositoryUri,
        private readonly IOInterface $io,
    ) {
        $parts = explode('/', $repositoryUri);
        if (3 !== count($parts)) {
            $message = sprintf('Invalid repository URI: %s. Format shall be <project>/<location>/<repository>', $repositoryUri);
            $this->io->error($message);
            throw new \InvalidArgumentException($message);
        }
        [$this->project, $this->location, $this->repository] = $parts;
        parent::__construct();
    }

    protected function initialize()
    {
        parent::initialize();

        $project = escapeshellarg($this->project);
        $location = escapeshellarg($this->location);
        $repository = escapeshellarg($this->repository);
        exec("gcloud artifacts files list --project={$project} --location={$location} --repository={$repository}", $output, $code);
        if (0 !== $code) {
            $this->io->warning("Could not resolve packages for repository {$project}/{$location}/{$repository}.");
            return;
        }
        array_shift($output); // Remove useless header

        $packages = [];
        foreach ($output as $line) {
            $parts = array_filter(explode(' ', $line));
            $file = $parts[0];
            [$name, $version, $zip] = explode(':', $file);
            $explodedName = explode('--', $name);
            if (2 !== count($explodedName)) {
                continue; // Invalid file name, we skip
            }
            [$vendorName, $packageName] = $explodedName;

            $package = new Package("{$vendorName}/{$packageName}", "dev-{$version}", "dev-{$version}");
            $package->setSourceUrl("google-artifact://{$project}/{$location}/{$repository}/{$zip}");
            var_dump($package);
            $this->addPackage($package);
        }
        $this->packages = $packages;
    }
}
