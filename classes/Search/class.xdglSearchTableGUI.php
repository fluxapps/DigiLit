<?php

use srag\DIC\DigiLit\DICTrait;

/**
 * Class xdglSearchTableGUI
 *
 * @author: Benjamin Seglias   <bs@studer-raimann.ch>
 */
class xdglSearchTableGUI extends ilTable2GUI
{
    use DICTrait;

    public const TBL_ID = 'xdgl_search';
    /**
     * @var array
     */
    protected $filter = [];
    protected \ilObjDigiLitAccess $access;
    protected \ilDigiLitPlugin $pl;
    protected $tpl;

    /**
     * ilLocationDataTableGUI constructor.
     *
     * @param xdglSearchGUI $a_parent_obj
     * @param string        $a_parent_cmd
     * @param string        $search_title
     * @param string        $search_author
     */
    public function __construct(?object $a_parent_obj, string $a_parent_cmd, string $a_template_context = "", $search_title, $search_author)
    {
        $this->parent_obj = $a_parent_obj;
        $this->access = new ilObjDigiLitAccess();
        $this->pl = ilDigiLitPlugin::getInstance();
        $this->setId(self::TBL_ID);
        $this->setPrefix(self::TBL_ID);
        $this->setFormName(self::TBL_ID);
        $this->tpl = self::dic()->ui()->mainTemplate();
        self::dic()->ctrl()->saveParameter($a_parent_obj, $this->getNavParameter());
        parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);
        $this->parent_obj = $a_parent_obj;
        $this->setRowTemplate(
            "tpl.search_row.html",
            "Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit"
        );
        //TODO: based on previous cmd change formactionbyclass
        //$this->setFormAction(self::dic()->ctrl()->getFormActionByClass(ilObjDigiLitGUI::class));
        $this->setFormAction(self::dic()->ctrl()->getFormActionByClass(xdglSearchGUI::class));
        $this->setExternalSorting(true);
        $this->addCommandButton(xdglSearchGUI::CMD_ADD_LITERATURE, $this->pl->txt('add_literature'));
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");
        $this->setExternalSegmentation(true);
        $this->setEnableHeader(true);
        $this->initColums();
        $this->parseData($search_title, $search_author);
    }

    /**
     * @param array $a_set
     */
    protected function fillRow(array $a_set): void
    {
        $rdg_input = new ilRadioGroupInputGUI('', 'chosen_literature');
        $rd_option = new ilRadioOption('', $a_set['id']);
        $rdg_input->addOption($rd_option);
        $this->tpl->setVariable('RADIO_BTN', $rdg_input->render());
        foreach (array_keys($this->getSelectableColumns()) as $k) {
            if ($this->isColumnSelected($k)) {
                if ($a_set[$k]) {
                    $this->tpl->setCurrentBlock('td');
                    $this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode(", ", $a_set[$k]) : $a_set[$k]));
                    $this->tpl->parseCurrentBlock();
                } else {
                    $this->tpl->setCurrentBlock('td');
                    $this->tpl->setVariable('VALUE', '&nbsp;');
                    $this->tpl->parseCurrentBlock();
                }
            }
            $this->tpl->setVariable('REQUEST_ID', $a_set['id']);
        }
    }

    protected function initColums()
    {
        $number_of_selected_columns = count($this->getSelectedColumns());
        //add one to the number of columns for the radio button
        $number_of_selected_columns++;
        $column_width = 100 / $number_of_selected_columns . '%';

        //add column for radio buttons
        $this->addColumn('', '', $column_width);
        $all_cols = $this->getSelectableColumns();
        foreach ($this->getSelectedColumns() as $col) {
            $this->addColumn($all_cols[$col]['txt'], $col, $column_width);
        }
    }

    /**
     * @param string $search_title
     * @param string $search_author
     */
    protected function parseData($search_title, $search_author)
    {
        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);

        $this->determineLimit();
        $this->determineOffsetAndOrder();

        $data = xdglRequest::findDistinctRequestsByTitleAndAuthor($search_title, $search_author, $this->limit);
        $count = count($data);

        $this->setMaxCount($count);
        $this->setData($data);
    }

    /**
     * @return array{status: array{txt: string, default: true}, author: array{txt: string, default: true}, title: array{txt: string, default: true}, book: array{txt: string, default: true}, publisher: array{txt: string, default: true}, location: array{txt: string, default: true}, publishing_year: array{txt: string, default: true}, pages: array{txt: string, default: true}}
     */
    public function getSelectableColumns(): array
    {
        return ["status" => ["txt" => $this->pl->txt("request_status"), "default" => true], "author" => ["txt" => $this->pl->txt("author"), "default" => true], "title" => ["txt" => $this->pl->txt("title"), "default" => true], "book" => ["txt" => $this->pl->txt("request_book"), "default" => true], "publisher" => ["txt" => $this->pl->txt("request_publisher"), "default" => true], "location" => ["txt" => $this->pl->txt("request_location"), "default" => true], "publishing_year" => ["txt" => $this->pl->txt("request_publishing_year"), "default" => true], "pages" => ["txt" => $this->pl->txt("request_pages"), "default" => true]];
    }
}
