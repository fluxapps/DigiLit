<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\DIC\DigiLit\DICTrait;
use srag\Plugins\DigiLit\Menu\Menu;

/**
 * DigiLit repository object plugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version 1.0.00
 *
 */
class ilDigiLitPlugin extends ilRepositoryObjectPlugin
{
    use DICTrait;

    public const PLUGIN_ID = 'xdgl';
    public const PLUGIN_NAME = 'DigiLit';

    private static ?ilDigiLitPlugin $instance = null;

    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            global $DIC;
            // ILIAS 8
            if (isset($DIC['component.factory']) && $DIC['component.factory'] instanceof ilComponentFactory) {
                $component_factory = $DIC['component.factory'];
                self::$instance = $component_factory->getPlugin(self::PLUGIN_ID);
            } // ILIAS 7
            else {
                self::$instance = new self();
            }
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    protected function uninstallCustom(): void
    {
        $this->db->dropTable(xdglConfig::TABLE_NAME, false);
        $this->db->dropTable(xdglLibrarian::TABLE_NAME, false);
        $this->db->dropTable(xdglLibrary::TABLE_NAME, false);
        $this->db->dropTable(xdglRequest::TABLE_NAME, false);
        $this->db->dropTable(xdglRequestUsage::TABLE_NAME, false);
    }

    public function allowCopy(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function promoteGlobalScreenProvider(): AbstractStaticPluginMainMenuProvider
    {
        return new Menu(self::dic()->dic(), $this);
    }
}
