<?php

/**
 * Class xdglConfig
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.00
 */
class xdglConfig extends ActiveRecord
{
    public const TABLE_NAME = 'xdgl_config';
    public const CONFIG_VERSION = 2;
    public const F_ROLES_ADMIN = 'permission';
    public const F_ROLES_MANAGER = 'permission_manager';
    public const F_MAIL_NEW_REQUEST = 'mail_new_request';
    public const F_MAIL_REJECTED = 'mail_rejected';
    public const F_MAIL_UPLOADED = 'mail_uploaded';
    public const F_MAIL_MOVED = 'mail_moved';
    public const F_MAIL = 'mail';
    public const F_CONFIG_VERSION = 'config_version';
    public const F_MAX_DIGILITS = 'max_digilits';
    public const F_EULA_TEXT = 'eula_text';
    public const F_USE_LIBRARIES = 'use_libraries';
    public const F_USE_SEARCH = 'use_search';
    public const F_OWN_LIBRARY_ONLY = 'own_library_only';
    public const F_USE_REGEX = 'use_regex';
    public const F_MAX_REQ_TEXT = 'max_requests_text';
    public const F_REGEX = 'regex';
    /**
     * @var array
     */
    protected static $cache = [];
    /**
     * @var array
     */
    protected static $cache_loaded = [];
    /**
     * @var bool
     */
    protected bool $ar_safe_read = false;

    /**
     * @return string
     */
    public function getConnectorContainerName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @return string
     * @deprecated
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @return bool
     */
    public static function isConfigUpToDate(): bool
    {
        return self::getConfigValue(self::F_CONFIG_VERSION) == self::CONFIG_VERSION;
    }

    /**
     * @return bool
     */
    public static function hasValidRegex()
    {
        if (!self::getConfigValue(self::F_USE_REGEX)) {
            return false;
        }

        return self::isRegexValid(self::getConfigValue(self::F_REGEX));
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function getConfigValue($name)
    {
        if (!self::$cache_loaded[$name]) {
            $obj = new self($name);
            try {
                self::$cache[$name] = json_decode($obj->getValue(), null, 512, JSON_THROW_ON_ERROR);
                self::$cache_loaded[$name] = true;
            } catch (Throwable $e) {
                self::$cache[$name] = null;
            }
        }

        return self::$cache[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public static function setConfigValue($name, $value): void
    {
        $obj = new self($name);
        $obj->setValue(json_encode($value, JSON_THROW_ON_ERROR));

        if (self::where(['name' => $name])->hasSets()) {
            $obj->update();
        } else {
            $obj->create();
        }
    }

    /**
     * @var string
     *
     * @db_has_field        true
     * @db_is_unique        true
     * @db_is_primary       true
     * @db_is_notnull       true
     * @db_fieldtype        text
     * @db_length           250
     */
    protected $name;
    /**
     * @var string
     *
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           4000
     */
    protected $value;

    /**
     * @param string $regex
     *
     * @return bool
     */
    public static function isRegexValid($regex): bool
    {
        return (bool) preg_match("/^\/.+\/[a-z]*$/i", $regex);
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
