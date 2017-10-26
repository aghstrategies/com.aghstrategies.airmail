<div class="crm-block crm-form-block crm-airmail-form-block">

  <table class="form-layout-compressed">
    <tr>
      <td class="label">{$form.secretcode.label}</td>
      <td>{$form.secretcode.html}<br />
        <span class="description">{ts}You may provide a secret code here and in the notification URL in order to discourage spoof event notifications.{/ts}</span>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.external_smtp_service.label}</td>
      <td>{$form.external_smtp_service.html}<br />
      </td>
    </tr>
  </table>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
  <div class="spacer"></div>

  <div class="help">
    <h3>{ts}Airmail Event Notification Configuration{/ts}</h3>
    {*<p>{ts}We should probably put a link here to the event notification setup screen on Airmail.{/ts}</p>*}
    <p>Based on the secret code provided above, your <em>HTTP Post URL</em> is...</p>
    <pre>{$url}</pre>
    <p>{ts}see README.md for more details on how to configure your external SMTP service{/ts}</p>
  </div>

</div>
