# Elastic Email Specific Configuration

ATTENTION: the HTTP Web Notifications (Webhooks) feature is only available in the Unlimited Pro and Email API PRO plans. This feature is needed for this extension, so make sure your plan includes it. Source: https://help.elasticemail.com/en/articles/2376855-how-to-manage-http-web-notifications-webhooks

## Settings in Elastic Email

Log in to Elastic Email, go to the Settings menu, then select the Notifications tab.

Create a new webhook by clicking on the (+) button on the right side and give it a name. For the notification link, copy the URL displayed in the CiviCRM setup screen for the extension, and select the Bounce/Error event.

## Negotiations with Elastic Email

Elastic Email's main business is providing a full email marketing solution, not just a SMTP relay. This causes some friction because it means their primary way of working is basically a second CRM with a lot of the same data in as CiviCRM has. This extension works on the idea that we're only interested in the SMTP relay part of Elastic Email's offering; keeping two CRMs in sync is a nightmare thing to have to do and would definitely be out of scope for this extension.

With the default set-up at Elastic Email several things will happen that you probably don't want:

### Unsubscribe

CiviMail generates unsubscribe links that will remove the contact from the group that the mailing was sent to (that's the 90% case anyway, it's a little more complex than that).

CiviCRM does NOT generate unsubscribe links for non-CiviMail emails. Such emails are typically transactional, so unsubscribe doesn't make sense.

However, **Elastic Email requires that its own Unsubscribe link is present, and will add it at the bottom of all emails that don't contain it. This causes big problems**:

- CiviCRM won't know about it.

- Elastic Email will suppress all future emails to that email address.

   - if someone is subscribed to two groups in CiviCRM, unsubscribing with the Elastic Email unsubscribe link effectively unsubscribes them from both (and any future groups they subscribe to and any future transactional emails like receipts)

   - if one incumbant of campaigner@example.org unsubscribes, a future staff member could not be resubscribed.

- It's not possible for CiviCRM to re-subscribe someone unsubscribed this way; you need to manually use a special form from Elastic Email which will send a very generic confirmation email. This seems unlikely to be successful.

To avoid this and protect your data, the extesion will add a hidden (in the HTML at least) version of the Elastic Email unsubscribe link. This effectively bypasses their checks, and bypasses Elastic Email's ability to monitor unsubscribes; it's not ideal.

It's better if we can play nicely, but this is a pretty fundamental mismatch of what we need (general SMTP relay for "marketing", transactional and other mail) vs the service they offer (marketing mail), so some compromise is required.

You can contact Elastic Email and request that they turn on a flag on your domain called "Track stats only". They don't make this publicly available, so you'll have to justify why. What it means is that someone clicking their Unsubscribe link gets recorded as an unsubscribe, but nothing happens. On its own, this breaks unsubscribe, which is not what anybody wants, but when used in conjunction with the related extension setting, it means that CiviCRM's unsubscribe links get wrapped in a special syntax that means Elastic Email will be able to register the unsubscribe before redirecting to CiviCRM's normal unsubscribe. If you enable that option without having negotiated this with Elastic Email then you will have the problems outlined above.

CiviCRM provides the "opt out" setting too, which is a global unsubscribe; ne'er again shall CiviMail mail that contact; this is more akin to Elastic Email's normal unsubscribe, except of course CiviCRM deals with contacts; Elastic Email sees only email addresses (i.e. in CiviCRM one contact may own several email addresses).

Elastic Email would prefer organisations had a separate sub account for transactional to bulk mail, but CiviCRM doesn't support this concept. And anyway, the same problem would apply then to transactional: Elastic Email would still be insisting on their unsubscribe link, which could be just plain wrong.
