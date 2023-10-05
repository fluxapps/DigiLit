<?php

/**
 * Form-Class xdglConfigFormGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.00
 *
 */
class xdglConfigFormGUI extends ilPropertyFormGUI
{
    public const A_COLS = 60;
    public const A_ROWS = 5;
    public const F_LIMIT = 'limit';
    protected \xdglConfigGUI $parent_gui;
    private \ilDigiLitPlugin $pl;

    /**
     * @param xdglConfigGUI $parent_gui
     */
    public function __construct(xdglConfigGUI $parent_gui)
    {
        parent::__construct();
        global $ilCtrl;
        $this->parent_gui = $parent_gui;
        $this->ctrl = $ilCtrl;
        $this->pl = ilDigiLitPlugin::getInstance();
        $this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
        $this->initForm();
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function txt($field)
    {
        return $this->pl->txt('admin_' . $field);
    }

    protected function initForm()
    {
        $this->setTitle($this->txt('form_title'));
        if (ilObjDigiLitAccess::isGlobalAdmin()) {
            // Roles Admin
            $global_roles = self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL);
            $se = new ilMultiSelectInputGUI($this->txt(xdglConfig::F_ROLES_ADMIN), xdglConfig::F_ROLES_ADMIN);
            $se->setWidth(400);
            $se->setOptions($global_roles);
            $this->addItem($se);

            // Roles Manager
            $se = new ilMultiSelectInputGUI($this->txt(xdglConfig::F_ROLES_MANAGER), xdglConfig::F_ROLES_MANAGER);
            $se->setWidth(400);
            $se->setOptions($global_roles);
            $this->addItem($se);

            $h = new ilFormSectionHeaderGUI();
            $h->setTitle($this->txt('common'));
            $this->addItem($h);
        }
        // Mehrere Bibliotheken verwenden
        $use_regex = new ilCheckboxInputGUI($this->txt(xdglConfig::F_USE_REGEX), xdglConfig::F_USE_REGEX);
        $use_regex->setInfo($this->txt(xdglConfig::F_USE_REGEX . '_info'));
        {
            $te = new ilTextInputGUI($this->txt(xdglConfig::F_REGEX), xdglConfig::F_REGEX);
            $te->setInfo($this->txt(xdglConfig::F_REGEX . '_info'));
            $use_regex->addSubItem($te);
        }
        $this->addItem($use_regex);

        $h = new ilCheckboxInputGUI($this->txt(xdglConfig::F_USE_LIBRARIES), xdglConfig::F_USE_LIBRARIES);
        {
            $only_own = new ilCheckboxInputGUI(
                $this->txt(xdglConfig::F_OWN_LIBRARY_ONLY),
                xdglConfig::F_OWN_LIBRARY_ONLY
            );
            $h->addSubItem($only_own);
        }
        $this->addItem($h);

        // Anzahl DigiLits pro Kurs
        //		$h = new ilCheckboxInputGUI($this->txt(self::F_LIMIT), self::F_LIMIT);
        $te = new ilTextInputGUI($this->txt(xdglConfig::F_MAX_DIGILITS), xdglConfig::F_MAX_DIGILITS);
        //		$h->addSubItem($te);

        $this->addItem($te);

        $h = new ilCheckboxInputGUI($this->txt(xdglConfig::F_USE_SEARCH), xdglConfig::F_USE_SEARCH);
        $this->addItem($h);

        //		// Mailadress
        //		$te = new ilTextInputGUI($this->txt(xdglConfig::F_MAIL), xdglConfig::F_MAIL);
        //		$te->setRequired(true);
        //		$this->addItem($te);

        // Max Requests reached info
        $info = new ilTextAreaInputGUI($this->txt(xdglConfig::F_MAX_REQ_TEXT), xdglConfig::F_MAX_REQ_TEXT);
        $info->setUseRte(true);
        $info->setRteTags(['a', 'p', 'ul', 'li', 'ol']);
        $info->setCols(self::A_COLS);
        $info->setRows(self::A_ROWS);
        $this->addItem($info);

        $h = new ilFormSectionHeaderGUI();
        $h->setTitle($this->txt('mail_textes'));
        $this->addItem($h);

        // Mail new Request
        $te = new ilTextAreaInputGUI($this->txt(xdglConfig::F_MAIL_NEW_REQUEST), xdglConfig::F_MAIL_NEW_REQUEST);
        $te->setCols(self::A_COLS);
        $te->setRows(self::A_ROWS);
        $pl = xdglNotification::getPlaceHoldersForType(xdglNotification::TYPE_NEW_REQUEST);
        $te->setInfo($this->getPlaceHoldersFormatted($pl));
        $this->addItem($te);

        // Mail Rejected
        $te = new ilTextAreaInputGUI($this->txt(xdglConfig::F_MAIL_REJECTED), xdglConfig::F_MAIL_REJECTED);
        $te->setCols(self::A_COLS);
        $te->setRows(self::A_ROWS);
        $pl = xdglNotification::getPlaceHoldersForType(xdglNotification::TYPE_REJECTED);
        $te->setInfo($this->getPlaceHoldersFormatted($pl));
        $this->addItem($te);

        // Mail Uploaded
        $te = new ilTextAreaInputGUI($this->txt(xdglConfig::F_MAIL_UPLOADED), xdglConfig::F_MAIL_UPLOADED);
        $te->setCols(self::A_COLS);
        $te->setRows(self::A_ROWS);
        $pl = xdglNotification::getPlaceHoldersForType(xdglNotification::TYPE_ULOADED);
        $te->setInfo($this->getPlaceHoldersFormatted($pl));
        $this->addItem($te);

        // Mail Uploaded
        $te = new ilTextAreaInputGUI($this->txt(xdglConfig::F_MAIL_MOVED), xdglConfig::F_MAIL_MOVED);
        $te->setCols(self::A_COLS);
        $te->setRows(self::A_ROWS);
        $pl = xdglNotification::getPlaceHoldersForType(xdglNotification::TYPE_MOVED);
        $te->setInfo($this->getPlaceHoldersFormatted($pl));
        $this->addItem($te);

        $h = new ilFormSectionHeaderGUI();
        $h->setTitle($this->txt('eula'));
        $this->addItem($h);

        // EULA
        $te = new ilTextAreaInputGUI($this->txt(xdglConfig::F_EULA_TEXT), xdglConfig::F_EULA_TEXT);
        $te->setUseRte(true);
        $te->setRteTags(['a', 'p', 'ul', 'li', 'ol']);
        $te->setCols(self::A_COLS);
        $te->setRows(self::A_ROWS);
        $this->addItem($te);

        $this->addCommandButtons();
    }

    public function fillForm(): void
    {
        $array = [];
        foreach ($this->getItems() as $item) {
            $this->getValuesForItem($item, $array);
        }
        $this->setValuesByArray($array);
    }

    /**
     * @param ilFormPropertyGUI $item
     * @param array             $array
     *
     * @internal param $key
     */
    private function getValuesForItem($item, array &$array): void
    {
        if (self::checkItem($item)) {
            $key = $item->getPostVar();
            $array[$key] = xdglConfig::getConfigValue($key);
            //			echo '<pre>' . print_r($array, 1) . '</pre>';
            if (self::checkForSubItem($item)) {
                foreach ($item->getSubItems() as $subitem) {
                    $this->getValuesForItem($subitem, $array);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function saveObject(): bool
    {
        if (!$this->checkInput()) {
            return false;
        }
        foreach ($this->getItems() as $item) {
            $this->saveValueForItem($item);
        }
        xdglConfig::setConfigValue(xdglConfig::F_CONFIG_VERSION, xdglConfig::CONFIG_VERSION);

        return true;
    }

    public function checkInput(): bool
    {
        /**
         * @var ilMultiSelectInputGUI $roles_admin
         * @var ilMultiSelectInputGUI $roles_manager
         * @var ilTextInputGUI        $regex
         * @var ilCheckboxInputGUI    $use_regex
         */
        $check = true;
        if (ilObjDigiLitAccess::isGlobalAdmin()) {
            $roles_admin = $this->getItemByPostVar(xdglConfig::F_ROLES_ADMIN);
            if ((is_countable($roles_admin->getValue()) ? count($roles_admin->getValue()) : 0) == 0) {
                $check = false;
                $roles_admin->setAlert($this->txt("check_role"));
            }

            $roles_manager = $this->getItemByPostVar(xdglConfig::F_ROLES_MANAGER);
            if ((is_countable($roles_manager->getValue()) ? count($roles_manager->getValue()) : 0) == 0) {
                $check = false;
                $roles_manager->setAlert($this->txt("check_role"));
            }
        }
        $use_regex = $this->getItemByPostVar(xdglConfig::F_USE_REGEX);
        if ($use_regex->getChecked()) {
            $regex = $this->getItemByPostVar(xdglConfig::F_REGEX);
            if (!xdglConfig::isRegexValid($regex->getValue())) {
                $check = false;
                $regex->setAlert($this->txt("invalid_regexp"));
            }
        }
        if (!$check) {
            global $lng;
            ilUtil::sendFailure($lng->txt("form_input_not_valid"));

            return false;
        }

        return parent::checkInput();
    }

    /**
     * @param ilFormPropertyGUI $item
     */
    private function saveValueForItem($item): void
    {
        if (self::checkItem($item)) {
            $key = $item->getPostVar();
            xdglConfig::setConfigValue($key, $this->getInput($key));
            if (self::checkForSubItem($item)) {
                foreach ($item->getSubItems() as $subitem) {
                    $this->saveValueForItem($subitem);
                }
            }
        }
    }

    /**
     * @param ilFormPropertyGUI $item
     *
     * @return bool
     */
    public static function checkForSubItem($item): bool
    {
        return !$item instanceof ilFormSectionHeaderGUI && !$item instanceof ilMultiSelectInputGUI;
    }

    /**
     * @param ilFormPropertyGUI $item
     *
     * @return bool
     */
    public static function checkItem($item)
    {
        return !$item instanceof ilFormSectionHeaderGUI;
    }

    protected function addCommandButtons()
    {
        $this->addCommandButton(xdglConfigGUI::CMD_SAVE, $this->txt('form_button_save'));
        $this->addCommandButton(xdglConfigGUI::CMD_CANCEL, $this->txt('form_button_cancel'));
    }

    /**
     * @param int  $filter
     * @param bool $with_text
     *
     * @return array
     */
    public static function getRoles($filter, $with_text = true)
    {
        global $rbacreview;
        $opt = [];
        $role_ids = [];
        foreach ($rbacreview->getRolesByFilter($filter) as $role) {
            $opt[$role['obj_id']] = $role['title'] . ' (' . $role['obj_id'] . ')';
            $role_ids[] = $role['obj_id'];
        }
        if ($with_text) {
            return $opt;
        } else {
            return $role_ids;
        }
    }

    /**
     * @param array $placeholders
     *
     * @return string
     */
    public function getPlaceHoldersFormatted(array $placeholders)
    {
        return $this->txt('placeholders') . ': [' . implode('] [', $placeholders) . ']';
    }
}
