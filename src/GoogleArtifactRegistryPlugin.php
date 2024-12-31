<?php

declare(strict_types=1);

namespace DiliTrust\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\Plugin\PrePoolCreateEvent;

final class GoogleArtifactRegistryPlugin implements PluginInterface, EventSubscriberInterface
{
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::PRE_FILE_DOWNLOAD => 'onPreFileDownload',
            PluginEvents::PRE_POOL_CREATE => 'onPrePoolCreate',
        ];
    }

    public function onPreFileDownload(PreFileDownloadEvent $event): void
    {
        $this->io->write($event->getProcessedUrl());
    }

    public function onPrePoolCreate(PrePoolCreateEvent $event): void
    {
        foreach ($event->getUnacceptableFixedPackages() as $package) {
            $this->io->write($package->getPrettyString());
        }
    }
}
