<?php

require_once 'saveondisk.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function saveondisk_civicrm_config(&$config) {
  _saveondisk_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function saveondisk_civicrm_xmlMenu(&$files) {
  _saveondisk_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function saveondisk_civicrm_install() {
  _saveondisk_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function saveondisk_civicrm_postInstall() {
  _saveondisk_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function saveondisk_civicrm_uninstall() {
  _saveondisk_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function saveondisk_civicrm_enable() {
  _saveondisk_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function saveondisk_civicrm_disable() {
  _saveondisk_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function saveondisk_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _saveondisk_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function saveondisk_civicrm_managed(&$entities) {
  _saveondisk_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_buildForm().
 *
 * Used to add a 'Save to Disk' button in the Report forms.
 * This is copied from the ca.bidon.civiexportexcel extension.
 */
function saveondisk_civicrm_buildForm($formName, &$form) {
  // Reports extend the CRM_Report_Form class.
  // We use that to check whether we should inject the Excel export buttons.
  if (is_subclass_of($form, 'CRM_Report_Form')) {
    $smarty = CRM_Core_Smarty::singleton();
    $vars = $smarty->get_template_vars();

    $form->_saveondiskButtonName = $form->getButtonName('submit', 'saveondisk');

    if (empty($vars['instanceId'])) {
      return;
    }

    $label = ts('Save to disk');
    $form->addElement('submit', $form->_saveondiskButtonName, $label);

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/Report/Form/Actions-saveondisk.tpl',
    ));

    // This hook also gets called when we click on a submit button,
    // so we can handle that part here too.
    $buttonName = $form->controller->getButtonName();

    $output = CRM_Utils_Request::retrieve('output', 'String', CRM_Core_DAO::$_nullObject);

    if ($form->_saveondiskButtonName == $buttonName || $output == 'saveondisk') {
      $form->assign('printOnly', TRUE);
      $printOnly = TRUE;
      $form->assign('outputMode', 'saveondisk');

      // FIXME: this duplicates part of CRM_Report_Form::postProcess()
      // since we do not have a place to hook into, we hi-jack the form process
      // before it gets into postProcess.

      // get ready with post process params
      $form->beginPostProcess();

      // build query
      $sql = $form->buildQuery(FALSE);

      // build array of result based on column headers. This method also allows
      // modifying column headers before using it to build result set i.e $rows.
      $rows = array();
      $form->buildRows($sql, $rows);

      // format result set.
      // This seems to cause more problems than it fixes.
      // $form->formatDisplay($rows);

      // Show stats on a second Excel page.
      $stats = $form->statistics($rows);

      // assign variables to templates
      $form->doTemplateAssignment($rows);
      // FIXME: END.

      $csv = CRM_Report_Utils_Report::makeCsv($form, $rows);

      // FIXME: is there a core function to save this type of files?
      $path = CRM_Core_Config::singleton()->customFileUploadDir . 'reports';

      // NB: the result is NULL if the path already exists.
      $result = CRM_Utils_File::createDir($path, FALSE);

      if ($result === TRUE) {
        Civi::log()->info("Created the directory $path for reports saved by the saveondisk extension.");
      }
      elseif ($result === FALSE) {
        Civi::log()->info("Failed to create directory $path for reports saved by the saveondisk extension.");
      }

      $filename = $path . '/instance-' . $vars['instanceId'] . '.csv';
      $success = file_put_contents($filename, $csv);

      if ($success) {
        CRM_Core_Session::setStatus(ts("The report has been saved on disk as %1", [
          1 => 'instance-' . $vars['instanceId'] . '.csv',
          'domain' => 'coop.symbiotic.saveondisk',
        ]), '', 'success');
      }
      else {
        Civi::log()->warning("Failed saving a report to $filename");

        CRM_Core_Session::setStatus(ts("Failed to save report on disk as %1. Check the directory permissions?", [
          1 => 'instance-' . $vars['instanceId'] . '.csv',
          'domain' => 'coop.symbiotic.saveondisk',
        ]), '', 'error');
      }
    }
  }
}
