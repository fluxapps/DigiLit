<?php

/**
 * Class xdglLibrary
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xdglLibrary extends ActiveRecord
{
    public const TABLE_NAME = 'xdgl_library';

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
     * @var int
     */
    protected $not_deletable_reason;
    /**
     * @var xdglLibrarian
     */
    protected $librarians = [];
    /**
     * @var int
     */
    protected $assigned_requests_count = 0;
    /**
     * @var int
     *
     * @con_is_primary true
     * @con_is_unique  true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_sequence   true
     */
    protected $id = '';
    /**
     * @var bool
     *
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     * @db_is_notnull       true
     */
    protected $active = true;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     */
    protected $title = '';
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     1024
     */
    protected $description = '';
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     */
    protected $ext_id = '';
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     1024
     */
    protected $email = '';
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
    protected $is_primary = false;

    /**
     * @return int
     */
    public static function getPrimaryId()
    {
        return self::getPrimary()->getId();
    }

    /**
     * @return xdglLibrary
     */
    public static function getPrimary()
    {
        /**
         * @var xdglLibrary $res
         */
        return self::where(['is_primary' => 1])->first();
    }

    /**
     * @param ilObjUser $ilObjUser
     *
     * @return array
     */
    public static function getLibraryIdsForUser(ilObjUser $ilObjUser)
    {
        return xdglLibrarian::where(['usr_id' => $ilObjUser->getId()])->getArray(null, 'library_id');
    }

    /**
     * @param ilObjUser $ilObjUser
     * @param int       $lib_id
     *
     * @return bool
     */
    public static function isAssignedToLibrary(ilObjUser $ilObjUser, $lib_id): bool
    {
        return in_array($lib_id, self::getLibraryIdsForUser($ilObjUser));
    }

    /**
     * @param ilObjUser $ilObjUser
     *
     * @return bool
     */
    public static function isAssignedToAnyLibrary(ilObjUser $ilObjUser): bool
    {
        return self::getLibraryIdsForUser($ilObjUser) !== [];
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        if ($this->getIsPrimary() !== 0) {
            $this->not_deletable_reason = 1;

            return false;
        }
        if ($this->getRequestCount() > 0) {
            $this->not_deletable_reason = 2;

            return false;
        }

        if ($this->getLibrarianCount() > 0) {
            $this->not_deletable_reason = 3;

            return false;
        }

        return true;
    }

    public function afterObjectLoad(): void
    {
        $this->setLibrarians(xdglLibrarian::where(['library_id' => $this->getId()])->get());
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if ($this->getIsPrimary() !== 0) {
            return false;
        }
        if (self::where(['is_primary' => 1])->count() == 0) {
            $this->makePrimary();

            return false;
        }
        parent::delete();
    }

    public function makePrimary(): void
    {
        global $ilDB;
        /**
         * @var ilDB $ilDB
         */
        $ilDB->manipulate('UPDATE ' . $this->getConnectorContainerName() . ' SET is_primary = 0');
        $this->setIsPrimary(true);
        $this->update();
    }

    /**
     * @return bool
     */
    public function getRequestCount()
    {
        static $count = null;
        if ($count === null) {
            $count = xdglRequest::where(['library_id' => $this->getId()])->count();
        }

        return $count;
    }

    /**
     * @return bool
     */
    public function getLibrarianCount(): int
    {
        return count($this->getLibrarians());
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getExtId()
    {
        return $this->ext_id;
    }

    /**
     * @param string $ext_id
     */
    public function setExtId($ext_id): void
    {
        $this->ext_id = $ext_id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getIsPrimary()
    {
        return $this->is_primary;
    }

    /**
     * @param int $is_primary
     */
    public function setIsPrimary($is_primary): void
    {
        $this->is_primary = $is_primary;
    }

    /**
     * @return int
     */
    public function getAssignedRequestsCount()
    {
        return $this->assigned_requests_count;
    }

    /**
     * @param int $assigned_requests_count
     */
    public function setAssignedRequestsCount($assigned_requests_count): void
    {
        $this->assigned_requests_count = $assigned_requests_count;
    }

    /**
     * @return xdglLibrarian[]
     */
    public function getLibrarians()
    {
        return $this->librarians;
    }

    /**
     * @param xdglLibrarian[] $librarians
     */
    public function setLibrarians($librarians): void
    {
        $this->librarians = $librarians;
    }

    /**
     * @return int
     */
    public function getNotDeletableReason()
    {
        return $this->not_deletable_reason;
    }

    /**
     * @param int $not_deletable_reason
     */
    public function setNotDeletableReason($not_deletable_reason): void
    {
        $this->not_deletable_reason = $not_deletable_reason;
    }
}
