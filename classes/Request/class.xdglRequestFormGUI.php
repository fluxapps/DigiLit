<?php

/**
 * GUI-Class xdglRequestFormGUI
 *
 * @author            Gabriel Comte <gc@studer-raimann.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version           1.0.00
 */
class xdglRequestFormGUI extends ilPropertyFormGUI
{
    public const F_COURSE_NAME = 'course_name';
    public const F_PUBLISHING_YEAR = 'publishing_year';
    public const F_PUBLISHER = 'publisher';
    public const F_LOCATION = 'location';
    public const F_EDITOR = 'editor';
    public const F_BOOK = 'book';
    public const F_TITLE = 'title';
    public const F_AUTHOR = 'author';
    public const F_PAGES = 'pages';
    public const F_PAGE_TO = 'page_to';
    public const F_VOLUME_YEAR = 'volume';
    public const F_EDITION_RELEVANT = 'edition_relevant';
    public const F_ISSN = 'issn';
    public const REGEX_MAX_FOUR_DIGITS = '/^[0-9]{0,4}$/i';
    public const REGEX_FOUR_DIGITS_ONLY = '/^[0-9]{4}$/';
    public const F_CRS_REF_ID = 'crs_ref_id';
    public const F_COUNT = 'count';
    public const F_REQUESTER_FULLNAME = 'requester_fullname';
    public const F_REQUESTER_MAILTO = 'requester_mailto';
    public const F_CREATE_DATE = 'create_date';
    public const F_LAST_STATUS_CHANGE = 'last_status_change';
    public const F_MODIFIED_BY = 'modified_by';
    public const F_CONFIRM_EULA = 'confirm_eula';
    public const F_NOTICE = 'notice';
    public const F_INTERNAL_NOTICE = 'internal_notice';
    private bool $is_new;
    private bool $view;
    private bool $infopage;
    protected \xdglRequest $request;
    /**
     * @var ilObjDigiLitGUI
     */
    protected $parent_gui;
    protected \ilDigiLitPlugin $pl;
    /**
     * @var bool
     */
    protected $external = true;

    /**
     * @param xdglRequestGUI $parent_gui
     * @param xdglRequest    $request
     *
     * @param bool           $view
     *
     * @param bool           $infopage
     *
     */
    public function __construct($parent_gui, xdglRequest $request, $view = false, $infopage = false, $external = true)
    {
        parent::__construct();
        global $ilCtrl, $lng;
        $this->request = $request;
        $this->parent_gui = $parent_gui;
        $this->ctrl = $ilCtrl;
        $this->pl = ilDigiLitPlugin::getInstance();
        $this->ctrl->saveParameter($parent_gui, xdglRequestGUI::XDGL_ID);
        $this->ctrl->saveParameter($parent_gui, 'new_type');
        $this->lng = $lng;
        $this->is_new = ($this->request->getId() == 0);
        $this->view = $view;
        $this->infopage = $infopage;
        $this->external = $external;
        if ($view) {
            $this->initView();
        } else {
            $this->initForm();
        }
    }

    /**
     * Workaround for returning an object of class ilPropertyFormGUI instead of this subclass
     * this is used, until bug (http://ilias.de/mantis/view.php?id=13168) is fixed
     *
     * @return ilPropertyFormGUI This object but as an ilPropertyFormGUI instead of a xdglRequestFormGUI
     */
    public function getAsPropertyFormGui(): \ilPropertyFormGUI
    {
        $ilPropertyFormGUI = new ilPropertyFormGUI();
        $ilPropertyFormGUI->setFormAction($this->getFormAction());
        $ilPropertyFormGUI->setTitle($this->getTitle());

        $ilPropertyFormGUI->addCommandButton(xdglRequestGUI::CMD_SAVE, $this->lng->txt(xdglRequestGUI::CMD_SAVE));
        $ilPropertyFormGUI->addCommandButton(xdglRequestGUI::CMD_CANCEL, $this->lng->txt('cancel'));
        foreach ($this->getItems() as $item) {
            $ilPropertyFormGUI->addItem($item);
        }

        return $ilPropertyFormGUI;
    }

    public function addToInfoScreen(ilInfoScreenGUI $ilInfoScreenGUI): void
    {
    }

    protected function initView()
    {
        if (!$this->infopage) {
            $te = new ilNonEditableValueGUI($this->txt(self::F_REQUESTER_FULLNAME), self::F_REQUESTER_FULLNAME);
            $this->addItem($te);

            $te = new ilNonEditableValueGUI($this->txt(self::F_REQUESTER_MAILTO), self::F_REQUESTER_MAILTO);
            $this->addItem($te);

            $te = new ilNonEditableValueGUI($this->txt(self::F_CREATE_DATE), self::F_CREATE_DATE);
            $this->addItem($te);

            $te = new ilNonEditableValueGUI($this->txt(self::F_LAST_STATUS_CHANGE), self::F_LAST_STATUS_CHANGE);
            $this->addItem($te);

            $te = new ilNonEditableValueGUI($this->txt(self::F_MODIFIED_BY), self::F_MODIFIED_BY);
            $this->addItem($te);
        }

        $this->initForm();
        /**
         * @var ilNonEditableValueGUI $item
         */
        foreach ($this->getItems() as $item) {
            $te = new ilNonEditableValueGUI($this->txt($item->getPostVar()), $item->getPostVar());
            $this->removeItemByPostVar($item->getPostVar());
            $this->addItem($te);
        }
    }

    protected function initForm()
    {
        $this->setTarget('_top');
        $this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
        $this->initButtons();

        // Anzahl DigiLits
        $te = new ilNonEditableValueGUI($this->txt(self::F_COUNT), self::F_COUNT);
        $this->addItem($te);

        // Course ID
        if ($this->is_new) {
            $te = new ilHiddenInputGUI(self::F_CRS_REF_ID);
            $this->addItem($te);
        }

        // Course Name
        $course_name = new ilTextInputGUI($this->txt(self::F_COURSE_NAME), self::F_COURSE_NAME);
        if ($this->is_new) {
            $course_name->setDisabled(true);
        }
        //		$course_name->setRequired(true);
        $this->addItem($course_name);

        // Author
        $bj = new ilTextInputGUI($this->txt(self::F_AUTHOR), self::F_AUTHOR);
        $bj->setRequired(true);
        $this->addItem($bj);

        //Add input for title and set value storred in session by createObject
        $ti = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
        $ti->setRequired(true);
        $this->addItem($ti);

        // in book/journal
        $bj = new ilTextInputGUI($this->txt(self::F_BOOK), self::F_BOOK);
        $bj->setRequired(true);
        $this->addItem($bj);

        // editor
        $pu = new ilTextInputGUI($this->txt(self::F_EDITOR), self::F_EDITOR);
        $this->addItem($pu);

        // place of publication
        $pp = new ilTextInputGUI($this->txt(self::F_LOCATION), self::F_LOCATION);
        $this->addItem($pp);

        // publishing_company
        $pc = new ilTextInputGUI($this->txt(self::F_PUBLISHER), self::F_PUBLISHER);
        $this->addItem($pc);

        // publishing year
        $ye = new ilTextInputGUI($this->txt(self::F_PUBLISHING_YEAR), self::F_PUBLISHING_YEAR);
        $ye->setMaxLength(4);
        $ye->setRequired(true);
        //Set Regex Check: must be 4 digits
        $ye->setValidationRegexp(self::REGEX_FOUR_DIGITS_ONLY);
        $ye->setValidationFailureMessage($this->pl->txt('validation_failure_4_digits_required'));
        $this->addItem($ye);

        // pages
        $pa = new ilTextInputGUI($this->txt(self::F_PAGES), self::F_PAGES);
        $pa->setRequired(true);
        $this->addItem($pa);

        // volume (Band)
        $vo = new ilTextInputGUI($this->txt(self::F_VOLUME_YEAR), self::F_VOLUME_YEAR);
        $this->addItem($vo);

        // nur diese Auflage
        $na = new ilCheckboxInputGUI($this->txt(self::F_EDITION_RELEVANT), self::F_EDITION_RELEVANT);
        $this->addItem($na);

        // ISSN number
        $in = new ilTextInputGUI($this->txt(self::F_ISSN), self::F_ISSN);
        $this->addItem($in);

        //  Notice
        $in = new ilTextAreaInputGUI($this->txt(self::F_NOTICE), self::F_NOTICE);
        $in->setCols(40);
        $in->setRows(6);
        $this->addItem($in);

        // Internal Notice
        if (!$this->external) {
            $in = new ilTextAreaInputGUI($this->txt(self::F_INTERNAL_NOTICE), self::F_INTERNAL_NOTICE);
            $this->addItem($in);
        }

        // EULA
        if ($this->is_new) {
            $eula = new ilCheckboxInputGUI($this->txt(self::F_CONFIRM_EULA), self::F_CONFIRM_EULA);
            $eula->setOptionTitle($this->txt(self::F_CONFIRM_EULA . '_title'));
            $eula->setRequired(true);
            $tpl = $this->pl->getTemplate('default/tpl.eula.html');
            $tpl->setVariable('TXT_SHOW', $this->txt(self::F_CONFIRM_EULA . '_show'));
            $tpl->setVariable('EULA', xdglConfig::getConfigValue(xdglConfig::F_EULA_TEXT));

            $eula->setInfo($tpl->get());
            $this->addItem($eula);
        }
    }

    /**
     * @param null $ref_id
     */
    public function fillFormRandomized($ref_id = null): void
    {
        if ($ref_id) {
            $this->request->setCrsRefId($ref_id);
        }
        $array = [self::F_AUTHOR => 'Author Name', self::F_TITLE => 'Article Name', self::F_BOOK => 'The Book', self::F_EDITOR => '', self::F_LOCATION => 'Berne', self::F_PUBLISHER => 'Publisher Name', self::F_PUBLISHING_YEAR => 2004, self::F_PAGES => '50-89', self::F_EDITION_RELEVANT => false, self::F_ISSN => '', self::F_VOLUME_YEAR => 2004, self::F_NOTICE => 'This Text only!', self::F_COURSE_NAME => $this->request->getCourseTitle()];
        $this->setValuesByArray($array);
    }

    /**
     * @param int $ref_id
     */
    public function fillForm($ref_id = null): void
    {
        if ($ref_id) {
            $this->request->setCrsRefId($ref_id);
        }
        $ilObjUserRequester = new ilObjUser($this->request->getRequesterUsrId());
        $ilObjUserModified = new ilObjUser($this->request->getLastModifiedByUsrId());

        $array = [self::F_AUTHOR => $this->request->getAuthor(), self::F_TITLE => $this->request->getTitle(), self::F_BOOK => $this->request->getBook(), self::F_EDITOR => $this->request->getEditor(), self::F_LOCATION => $this->request->getLocation(), self::F_PUBLISHER => $this->request->getPublisher(), self::F_PUBLISHING_YEAR => $this->request->getPublishingYear(), self::F_PAGES => $this->request->getPages(), self::F_EDITION_RELEVANT => $this->request->getEditionRelevant(), self::F_ISSN => $this->request->getIssn(), self::F_COUNT => $this->request->getAmoutOfDigiLitsInCourse() . '/' . xdglConfig::getConfigValue(xdglConfig::F_MAX_DIGILITS), self::F_REQUESTER_FULLNAME => $ilObjUserRequester->getPresentationTitle(), self::F_REQUESTER_MAILTO => $ilObjUserRequester->getEmail(), self::F_CREATE_DATE => date('d.m.Y - H:i:s', $this->request->getCreateDate()), self::F_LAST_STATUS_CHANGE => date('d.m.Y - H:i:s', $this->request->getDateLastStatusChange()), self::F_MODIFIED_BY => $ilObjUserModified->getPresentationTitle(), self::F_VOLUME_YEAR => $this->request->getVolume(), self::F_NOTICE => $this->request->getNotice(), self::F_INTERNAL_NOTICE => $this->request->getInternalNotice(), self::F_COURSE_NAME => $this->request->getCourseTitle()];
        if ($this->is_new) {
            $amount_of_existing_digi_lits = $this->request->getAmoutOfDigiLitsInCourse();
            $array[self::F_COUNT] = $amount_of_existing_digi_lits + 1 . '/' . xdglConfig::getConfigValue(xdglConfig::F_MAX_DIGILITS);
        }
        if ($this->view) {
            $array[self::F_EDITION_RELEVANT] = xdglRequest::boolTextRepresentation($this->request->getEditionRelevant());
        } else {
            $array[self::F_EDITION_RELEVANT] = $this->request->getEditionRelevant();
        }

        $this->setValuesByArray($array);
    }

    /**
     * returns whether checkinput was successful or not.
     *
     * @return bool
     */
    public function fillObject($ref_id): bool
    {
        if (!$this->checkInput()) {
            return false;
        }
        if ($this->is_new && !$this->getInput(self::F_CONFIRM_EULA)) {
            /**
             * @var ilCheckboxInputGUI $item
             */
            $item = $this->getItemByPostVar(self::F_CONFIRM_EULA);
            $item->setAlert($this->txt(self::F_CONFIRM_EULA . '_warning'));

            return false;
        }
        $this->request->setCourseNumber($this->getInput(self::F_COURSE_NAME));
        $this->request->setAuthor($this->getInput(self::F_AUTHOR));
        $this->request->setTitle($this->getInput(self::F_TITLE));
        $this->request->setBook($this->getInput(self::F_BOOK));
        $this->request->setEditor($this->getInput(self::F_EDITOR));
        $this->request->setLocation($this->getInput(self::F_LOCATION));
        $this->request->setPublisher($this->getInput(self::F_PUBLISHER));
        $this->request->setPublishingYear($this->getInput(self::F_PUBLISHING_YEAR));
        $this->request->setPages($this->getInput(self::F_PAGES));
        $this->request->setNotice($this->getInput(self::F_NOTICE));
        if (!$this->external) {
            $this->request->setInternalNotice($this->getInput(self::F_INTERNAL_NOTICE));
        }
        if ($this->getInput(self::F_VOLUME_YEAR) === '') {
            $this->request->setVolume(null);
        } else {
            $this->request->setVolume($this->getInput(self::F_VOLUME_YEAR));
        }
        $this->request->setEditionRelevant($this->getInput(self::F_EDITION_RELEVANT));
        $this->request->setIssn($this->getInput(self::F_ISSN));

        if ($this->is_new && $ref_id) {
            $this->request->setCrsRefId($ref_id);
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function txt($key)
    {
        return $this->pl->txt('request_' . $key);
    }

    /**
     * @return bool false when unsuccessful or int request_id when successful
     */
    public function saveObject($ref_id)
    {
        if (!$this->fillObject($ref_id)) {
            return false;
        }
        if ($this->request->getId() > 0) {
            $this->request->update();
        } else {
            $this->request->create();
            xdglNotification::sendNew($this->request);
        }

        return $this->request->getId();
    }

    protected function initButtons()
    {
        if ($this->view) {
            $this->setTitle($this->pl->txt('request_view'));
            $this->addCommandButton(xdglRequestGUI::CMD_EDIT, $this->pl->txt('request_edit'));
            if ($this->request->getStatus() != xdglRequest::STATUS_RELEASED) {
                $this->addCommandButton(xdglRequestGUI::CDM_CONFIRM_REFUSE, $this->pl->txt('request_refuse'));
                $this->addCommandButton(xdglRequestGUI::CMD_SELECT_FILE, $this->pl->txt('upload_title'));
            } else {
                $this->addCommandButton(xdglRequestGUI::CMD_REPLACE_FILE, $this->pl->txt('request_replace_file'));
                $this->addCommandButton(xdglRequestGUI::CMD_DELETE_FILE, $this->pl->txt('request_delete_file'));
            }
        } elseif ($this->is_new) {
            $this->setTitle($this->pl->txt('request_create'));
            $this->addCommandButton(xdglRequestGUI::CMD_SAVE, $this->pl->txt('request_create'));
        } else {
            $this->setTitle($this->pl->txt('request_edit'));
            $this->addCommandButton(xdglRequestGUI::CMD_UPDATE, $this->pl->txt('request_update'));
        }

        $this->addCommandButton(xdglRequestGUI::CMD_CANCEL, $this->pl->txt('request_cancel'));
    }
}
