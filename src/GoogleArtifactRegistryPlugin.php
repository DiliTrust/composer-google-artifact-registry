<?php

declare(strict_types=1);

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent;

final class GoogleArtifactRegistryPlugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
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
        ];
    }

    public function onPreFileDownload(PreFileDownloadEvent $event): void
    {
        $protocol = parse_url($event->getProcessedUrl(), PHP_URL_SCHEME);
        if ($protocol !== 'google-artifact') {
            return;
        }
        // TODO: resolve dependency with artifact
        // TODO: set processed url to the local cache path
    }
}
