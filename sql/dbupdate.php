<#1>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
xdglRequest::installDB();
?>
<#2>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Config/class.xdglConfig.php';
xdglConfig::installDB();
?>
<#3>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php';
xdglLibrary::installDB();
if (!xdglLibrary::where(array( 'is_primary' => 1 ))->hasSets()) {
	$xdglLibrary = new xdglLibrary();
	$xdglLibrary->setTitle('Primary Library');
	$xdglLibrary->setDescription('');
	$xdglLibrary->setActive(true);
	$xdglLibrary->setIsPrimary(true);
	$xdglLibrary->setEmail(xdglConfig::get(xdglConfig::F_MAIL));
	$xdglLibrary->create();
}
xdglConfig::set(xdglConfig::F_USE_LIBRARIES, true);
?>
<#4>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php';
xdglRequest::updateDB();
global $ilDB;
/**
 * @var $ilDB ilDB
 */

$ilDB->manipulate('UPDATE ' . xdglRequest::returnDbTableName() . ' SET library_id = ' . $ilDB->quote(xdglLibrary::getPrimaryId(), 'integer'));
$ilDB->manipulate('UPDATE ' . xdglRequest::returnDbTableName() . ' SET librarian_id = ' . $ilDB->quote(0, 'integer') . ' WHERE librarian_id IS NULL');
?>
<#5>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarian.php');
xdglLibrarian::installDB();
?>