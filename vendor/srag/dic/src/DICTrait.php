<?php

namespace srag\DIC\DigiLit;

use srag\DIC\DigiLit\DIC\DICInterface;
use srag\DIC\DigiLit\Exception\DICException;
use srag\DIC\DigiLit\Output\OutputInterface;
use srag\DIC\DigiLit\Plugin\PluginInterface;
use srag\DIC\DigiLit\Version\VersionInterface;

/**
 * Trait DICTrait
 *
 * @package srag\DIC\DigiLit
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DICTrait
{
    /* *
     * @var string
     *
     * @abstract
     *
     * TODO: Implement Constants in Traits in PHP Core
     * /
    const PLUGIN_CLASS_NAME = "";*/

    /**
     * Get DIC interface
     *
     * @return DICInterface DIC interface
     */
    final protected static function dic(): DICInterface
    {
        return DICStatic::dic();
    }


    /**
     * Get output interface
     *
     * @return OutputInterface Output interface
     */
    final protected static function output(): OutputInterface
    {
        return DICStatic::output();
    }


    /**
     * Get plugin interface
     *
     * @return PluginInterface Plugin interface
     *
     * @throws DICException Class $plugin_class_name not exists!
     * @throws DICException Class $plugin_class_name not extends ilPlugin!
     * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
     */
    final protected static function plugin(): PluginInterface
    {
        self::checkPluginClassNameConst();

        return DICStatic::plugin(static::PLUGIN_CLASS_NAME);
    }


    /**
     * Get version interface
     *
     * @return VersionInterface Version interface
     */
    final protected static function version(): VersionInterface
    {
        return DICStatic::version();
    }


    /**
     * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
     */
    private static function checkPluginClassNameConst()/*: void*/
    {
        if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
            throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!", DICException::CODE_MISSING_CONST_PLUGIN_CLASS_NAME);
        }
    }
}
