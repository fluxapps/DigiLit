<?php

/**
 * Class xdglNotification
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 *
 */
class xdglNotification extends ilMailNotification
{
    
    protected ?string $internal_notification_type = null;
    public const TYPE_NEW_REQUEST = xdglConfig::F_MAIL_NEW_REQUEST;
    public const TYPE_REJECTED = xdglConfig::F_MAIL_REJECTED;
    public const TYPE_ULOADED = xdglConfig::F_MAIL_UPLOADED;
    public const TYPE_MOVED = xdglConfig::F_MAIL_MOVED;
    public const R_TITLE = 'REQUEST_TITLE';
    public const R_AUTHOR = 'REQUEST_AUTHOR';
    public const R_REQUESTER = 'REQUESTER';
    public const ADMIN_LINK = 'ADMIN_LINK';
    public const R_REQUESTER_FULLNAME = 'REQUESTER_FULLNAME';
    public const R_REASON = 'REASON';
    public const R_COURSE_NUMBER = 'COURSE_NUMBER';
    public const R_BOOK = 'BOOK';
    public const R_EDITOR = 'EDITOR';
    public const R_LOCATION = 'LOCATION';
    public const R_PUBLISHER = 'PUBLISHER';
    public const R_PUBLISHING_YEAR = 'PUBLISHING_YEAR';
    public const R_PAGES = 'PAGES';
    public const R_VOLUME = 'VOLUME';
    public const R_EDITION_RELEVANT = 'EDITION_RELEVANT';
    public const R_ISSN = 'ISSN';
    public const R_LAST_MODIFIED_BY_USER = 'LAST_MODIFIED_BY_USER';
    public const R_ASSIGNED_LIBRARY = 'ASSIGNED_LIBRARY';
    public const R_ASSIGNED_LIBRARIAN = 'ASSIGNED_LIBRARIAN';
    public const R_NOTICE = 'NOTICE';
    public const R_ALL = 'ALL';
    public const R_INTERNAL_NOTICE = 'INTERNAL_NOTICE';
    /**
     * @var array
     */
    protected static $placeholders = [
        self::TYPE_NEW_REQUEST => [
            xdglNotification::R_TITLE,
            xdglNotification::R_AUTHOR,
            xdglNotification::R_REQUESTER_FULLNAME,
            xdglNotification::R_COURSE_NUMBER,
            xdglNotification::R_BOOK,
            xdglNotification::R_EDITOR,
            xdglNotification::R_LOCATION,
            xdglNotification::R_PUBLISHER,
            xdglNotification::R_PUBLISHING_YEAR,
            xdglNotification::R_PAGES,
            xdglNotification::R_VOLUME,
            xdglNotification::R_EDITION_RELEVANT,
            xdglNotification::R_ISSN,
            xdglNotification::R_LAST_MODIFIED_BY_USER,
            xdglNotification::R_ASSIGNED_LIBRARY,
            xdglNotification::R_ASSIGNED_LIBRARIAN,
            xdglNotification::R_NOTICE,
            xdglNotification::R_ALL
        ],
        self::TYPE_ULOADED => [
            xdglNotification::R_TITLE,
            xdglNotification::R_AUTHOR,
            xdglNotification::R_COURSE_NUMBER,
            xdglNotification::R_BOOK,
            xdglNotification::R_EDITOR,
            xdglNotification::R_LOCATION,
            xdglNotification::R_PUBLISHER,
            xdglNotification::R_PUBLISHING_YEAR,
            xdglNotification::R_PAGES,
            xdglNotification::R_VOLUME,
            xdglNotification::R_EDITION_RELEVANT,
            xdglNotification::R_ISSN,
            xdglNotification::R_ASSIGNED_LIBRARY,
            xdglNotification::R_ASSIGNED_LIBRARIAN,
            xdglNotification::R_NOTICE,
            xdglNotification::R_ALL
        ],
        self::TYPE_REJECTED => [
            xdglNotification::R_TITLE,
            xdglNotification::R_AUTHOR,
            xdglNotification::R_REASON,
            xdglNotification::R_COURSE_NUMBER,
            xdglNotification::R_BOOK,
            xdglNotification::R_EDITOR,
            xdglNotification::R_LOCATION,
            xdglNotification::R_PUBLISHER,
            xdglNotification::R_PUBLISHING_YEAR,
            xdglNotification::R_PAGES,
            xdglNotification::R_VOLUME,
            xdglNotification::R_EDITION_RELEVANT,
            xdglNotification::R_ISSN,
            xdglNotification::R_ASSIGNED_LIBRARY,
            xdglNotification::R_ASSIGNED_LIBRARIAN,
            xdglNotification::R_NOTICE,
            xdglNotification::R_ALL
        ],
        self::TYPE_MOVED => [
            xdglNotification::R_TITLE,
            xdglNotification::R_AUTHOR,
            xdglNotification::R_REQUESTER_FULLNAME,
            xdglNotification::R_COURSE_NUMBER,
            xdglNotification::R_BOOK,
            xdglNotification::R_EDITOR,
            xdglNotification::R_LOCATION,
            xdglNotification::R_PUBLISHER,
            xdglNotification::R_PUBLISHING_YEAR,
            xdglNotification::R_PAGES,
            xdglNotification::R_VOLUME,
            xdglNotification::R_EDITION_RELEVANT,
            xdglNotification::R_ISSN,
            xdglNotification::R_LAST_MODIFIED_BY_USER,
            xdglNotification::R_ASSIGNED_LIBRARY,
            xdglNotification::R_ASSIGNED_LIBRARIAN,
            //			xdglNotification::R_INTERNAL_NOTICE,
            xdglNotification::R_NOTICE,
            xdglNotification::R_ALL,
        ]
    ];
    
    /**
     * @param int $type
     *
     * @return array
     */
    public static function getPlaceHoldersForType($type)
    {
        return self::$placeholders[$type];
    }
    
    protected ?xdglRequest $xdglRequest = null;
    protected ?ilObjUser $ilObjUser = null;
    
    public static function sendNew(xdglRequest $xdglRequest) : bool
    {
        $obj = new self();
        $obj->setInternalNotificationType(self::TYPE_NEW_REQUEST);
        $obj->setXdglRequest($xdglRequest);
        
        return $obj->send();
    }
    
    public static function sendUploaded(xdglRequest $xdglRequest) : bool
    {
        $obj = new self();
        $obj->setInternalNotificationType(self::TYPE_ULOADED);
        $obj->setXdglRequest($xdglRequest);
        
        return $obj->send();
    }
    
    public static function sendRejected(xdglRequest $xdglRequest) : bool
    {
        $obj = new self();
        $obj->setInternalNotificationType(self::TYPE_REJECTED);
        $obj->setXdglRequest($xdglRequest);
        
        return $obj->send();
    }
    
    public static function sendMoved(xdglRequest $xdglRequest) : bool
    {
        $obj = new self();
        $obj->setInternalNotificationType(self::TYPE_MOVED);
        $obj->setXdglRequest($xdglRequest);
        
        return $obj->send();
    }
    
    public function setInternalNotificationType(string $type) : void
    {
        $this->internal_notification_type = $type;
    }
    
    public function getInternalNotificationType() : ?string
    {
        return $this->internal_notification_type;
    }
    
    protected function getReplace(string $field) : string
    {
        global $ilCtrl;
        /**
         * @var ilCtrl $ilCtrl
         */
        $this->initUser();
        
        switch ($field) {
            case self::R_ALL:
                $return = '';
                foreach (self::$placeholders[$this->getInternalNotificationType()] as $v) {
                    if ($v != self::R_ALL) {
                        $return .= $v . ': ' . self::getReplace($v) . "\n";
                    }
                }
                
                return $return;
            
            case self::ADMIN_LINK:
                $ilCtrl->setParameterByClass(
                    xdglRequestGUI::class,
                    xdglRequestGUI::XDGL_ID,
                    $this->getXdglRequest()->getId()
                );
            
            // no break
            case self::R_TITLE:
                return $this->getXdglRequest()->getTitle();
            case self::R_AUTHOR:
                return $this->getXdglRequest()->getAuthor();
            case self::R_REQUESTER:
                return $this->getXdglRequest()->getRequesterUsrId();
            case self::R_REQUESTER_FULLNAME:
                return $this->ilObjUser->getFullname();
            case self::R_REASON:
                return $this->getXdglRequest()->getRejectionReason();
            case self::R_COURSE_NUMBER:
                return $this->getXdglRequest()->getCourseNumber();
            case self::R_BOOK:
                return $this->getXdglRequest()->getBook();
            case self::R_EDITOR:
                return $this->getXdglRequest()->getEditor();
            case self::R_LOCATION:
                return $this->getXdglRequest()->getLocation();
            case self::R_PUBLISHING_YEAR:
                return $this->getXdglRequest()->getPublishingYear();
            case self::R_PUBLISHER:
                return $this->getXdglRequest()->getPublisher();
            case self::R_PAGES:
                return $this->getXdglRequest()->getPages();
            case self::R_VOLUME:
                return $this->getXdglRequest()->getVolume();
            case self::R_INTERNAL_NOTICE:
                return $this->getXdglRequest()->getInternalNotice();
            case self::R_NOTICE:
                return $this->getXdglRequest()->getNotice();
            case self::R_EDITION_RELEVANT:
                return $this->getXdglRequest()->getEditionRelevant() !== 0 ? 'YES' : 'NO';
            case self::R_ISSN:
                return $this->getXdglRequest()->getIssn();
            case self::R_LAST_MODIFIED_BY_USER:
                $usr_id = $this->getXdglRequest()->getLastModifiedByUsrId();
                $obj = new ilObjUser($usr_id);
                
                return $obj->getFullname();
                break;
            case self::R_ASSIGNED_LIBRARY:
                $lib_id = $this->getXdglRequest()->getLibraryId();
                /**
                 * @var xdglLibrary $xdglLibrary
                 */
                $xdglLibrary = xdglLibrary::find($lib_id);
                if ($xdglLibrary instanceof xdglLibrary) {
                    return $xdglLibrary->getTitle();
                }
                break;
            case self::R_ASSIGNED_LIBRARIAN:
                $lib_id = $this->getXdglRequest()->getLibrarianId();
                if ($lib_id === 0) {
                    return 'NOBODY';
                }
                /**
                 * @var xdglLibrarian $xdglLibrary
                 */
                
                $activeRecordList = xdglLibrarian::where(['usr_id' => $lib_id]);
                if ($activeRecordList->hasSets()) {
                    $xdglLibrarian = $activeRecordList->first();
                    if ($xdglLibrarian instanceof xdglLibrarian) {
                        $usr_id = $xdglLibrarian->getUsrId();
                        $obj = new ilObjUser($usr_id);
                        
                        return $obj->getFullname() . ' (' . $obj->getEmail() . ')';
                    }
                }
                
                return "";
        }
        
        return '';
    }
    
    public function getAdress() : ?string
    {
        switch ($this->getInternalNotificationType()) {
            case self::TYPE_MOVED:
            case self::TYPE_NEW_REQUEST:
                $lib_id = $this->getXdglRequest()->getLibraryId();
                /** @var xdglLibrary $xdglLibrary */
                $xdglLibrary = xdglLibrary::find($lib_id);
                if ($xdglLibrary instanceof xdglLibrary) {
                    return $xdglLibrary->getEmail();
                }
                
                return xdglConfig::getConfigValue(xdglConfig::F_MAIL);
            case self::TYPE_ULOADED:
            case self::TYPE_REJECTED:
                return $this->ilObjUser->getEmail();
        }
        
        return null;
    }
    
    protected function generateBody() : string
    {
        $placeholders = self::getPlaceHoldersForType($this->getInternalNotificationType());
        $body = xdglConfig::getConfigValue($this->getInternalNotificationType());
        foreach ($placeholders as $k) {
            $body = str_replace('[' . $k . ']', $this->getReplace($k), $body);
        }
        return $body;
    }
    
    public function send() : bool
    {
        global $DIC;
        /**
         * @var ilObjUser $ilUser
         */
        $f = new ilMailMimeSenderFactory(
            $DIC->settings()
        );
        $this->initUser();
        $this->initLanguage($this->ilObjUser->getId());
        
        $mail = new ilMimeMail();
        $mail->From($f->user($DIC->user()->getId()));
        $subject = $this->getXdglRequest()->getExtId() . ': ' . ilDigiLitPlugin::getInstance()->txt(
                'notification_subject_' . $this->getInternalNotificationType()
            );
        $mail->Subject($subject);
        $mail->Body($this->generateBody());
        $mail->To($this->getAdress());
        
        return $mail->Send();
    }
    
    public function getXdglRequest() : xdglRequest
    {
        return $this->xdglRequest;
    }
    
    public function setXdglRequest(xdglRequest $xdglRequest) : void
    {
        $this->xdglRequest = $xdglRequest;
    }
    
    protected function initUser() : void
    {
        if ($this->ilObjUser === null) {
            $this->ilObjUser = new ilObjUser($this->getXdglRequest()->getRequesterUsrId());
        }
    }
}
