<?php

namespace srag\Plugins\DigiLit\Menu;

use ilDigiLitPlugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilObjDigiLitAccess;
use ilUIPluginRouterGUI;
use srag\DIC\DigiLit\DICTrait;
use xdglMainGUI;

/**
 * Class Menu
 *
 * @package srag\Plugins\DigiLit\Menu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Menu extends AbstractStaticPluginMainMenuProvider
{
    
    use DICTrait;
    
    const PLUGIN_CLASS_NAME = ilDigiLitPlugin::class;
    
    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        return [
            $this->symbol(
                $this->mainmenu->topParentItem($this->if->identifier(ilDigiLitPlugin::PLUGIN_ID . "_top"))
                               ->withTitle(ilDigiLitPlugin::PLUGIN_NAME)
                               ->withAvailableCallable(function () : bool {
                                   return true;
                               })
                               ->withVisibilityCallable(function () : bool {
                                   return ilObjDigiLitAccess::hasAccessToMainGUI();
                               })
            )
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];
        
        $plugin = ilDigiLitPlugin::getInstance();
        
        return [
            $this->symbol(
                $this->mainmenu->link($this->if->identifier(ilDigiLitPlugin::PLUGIN_ID . "_main"))
                               ->withParent($parent->getProviderIdentification())
                               ->withTitle($plugin->txt("main_menu_button"))
                               ->withAction(
                                   $this->dic->ctrl()->getLinkTargetByClass([
                                       ilUIPluginRouterGUI::class,
                                       xdglMainGUI::class
                                   ])
                               )
                               ->withAvailableCallable(function () : bool {
                                   return true;
                               })
                               ->withVisibilityCallable(function () : bool {
                                   return ilObjDigiLitAccess::hasAccessToMainGUI();
                               })
            )
        ];
    }
    
    /**
     * @param AbstractBaseItem $entry
     *
     * @return AbstractBaseItem
     */
    protected function symbol(AbstractBaseItem $entry) : AbstractBaseItem
    {
        if (self::version()->is6()) {
            $entry = $entry->withSymbol(
                self::dic()->ui()->factory()->symbol()->icon()->standard(
                    Standard::FILE,
                    ilDigiLitPlugin::PLUGIN_NAME
                )
            );
        }
        
        return $entry;
    }
}
