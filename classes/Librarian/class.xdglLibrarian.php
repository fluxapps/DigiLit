<?php

/**
 * Class xdglLibrarian
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglLibrarian extends ActiveRecord
{
    public const TABLE_NAME = 'xdgl_librarian';

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
    public function isDeletable(): bool
    {
        return !xdglRequest::where(['librarian_id' => $this->getUsrId(), 'status' => [xdglRequest::STATUS_REFUSED, xdglRequest::STATUS_RELEASED]], ['librarian_id' => '=', 'status' => 'NOT IN'])->hasSets();
    }


    /**
     * @param int $usr_id
     * @param int $library_id
     *
     * @return xdglLibrarian
     */
    public static function findLibrarian($usr_id, $library_id)
    {
        $obj = self::where(['usr_id' => $usr_id, 'library_id' => $library_id])->first();
        if ($obj instanceof xdglLibrarian) {
            return $obj;
        }

        return null;
    }

    /**
     * @param int $usr_id
     * @param int $library_id
     *
     * @return xdglLibrarian
     */
    public static function findOrGetInstanceOfLibrarian($usr_id, $library_id)
    {
        $obj = self::findLibrarian($usr_id, $library_id);
        if ($obj === null) {
            $obj = new self();
            $obj->setLibraryId($library_id);
            $obj->setUsrId($usr_id);
            $obj->is_new = true;
        }

        return $obj;
    }

    /**
     * @param null $lib_id
     *
     * @return array
     */
    public static function getAssignedLibrariansForLibrary($lib_id = null, $exclude = null, $all = false)
    {
        if ($lib_id === null) {
            $lib_id = xdglLibrary::getPrimaryId();
        }
        $list = self::getCollection();
        if (!$all) {
            $list->where(['library_id' => $lib_id]);
        }
        $list->innerjoin('usr_data', 'usr_id', 'usr_id', ['firstname', 'lastname', 'email']);
        $list->concat(['firstname', '" "', 'lastname', '" ("', 'email', '")"'], 'user_fullname');
        if ($exclude) {
            $list->where(['usr_id' => $exclude], '!=');
        }

        $list->orderBy('user_fullname');

        return $list->getArray('usr_id', 'user_fullname');
    }

    /**
     * @var int
     *
     * @con_has_field  true
     * @con_is_primary true
     * @con_is_unique  true
     * @con_sequence   true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $id = 0;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $usr_id = 0;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $library_id = 0;
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
    protected $active = true;

    /**
     * @return int
     */
    public function getUsrId()
    {
        return $this->usr_id;
    }

    /**
     * @param int $usr_id
     */
    public function setUsrId($usr_id): void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return int
     */
    public function getLibraryId()
    {
        return $this->library_id;
    }

    /**
     * @param int $library_id
     */
    public function setLibraryId($library_id): void
    {
        $this->library_id = $library_id;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
