{* We need this reference for the js injection below *}
{assign var=csv value="_qf_"|cat:$form.formName|cat:"_submit_csv"}

{* The nbps; are a mimic of what other buttons do in templates/CRM/Report/Form/Actions.tpl *}
{assign var=saveondisk value="_qf_"|cat:$form.formName|cat:"_submit_saveondisk"}
{$form.$saveondisk.html}&nbsp;&nbsp;

{literal}
  <script>
    CRM.$(function($) {
      var form_id = '{/literal}{$form.$saveondisk.id}{literal}';

      if ($('.crm-report-field-form-block .crm-submit-buttons').size() > 0) {
        $('input#' + form_id).appendTo('.crm-report-field-form-block .crm-submit-buttons');
      }
      else {
        // Do not show the button when running in a dashlet
        // FIXME: we should probably just not add the HTML in the first place.
        $('input#' + form_id).hide();
      }
    });
  </script>
{/literal}
