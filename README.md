This Extension:
--------------

The idea of this extension is to get it to work with a variety of SMTP Services.

We are starting with bounce processing for Amazon SES

## Configuration for SES/SNS

### Settings - For CiviMail

Make sure your Outbound Mail settings are configured to match the settings in your AWS SNS account

Log onto AWS Navigate to the Service SES and then to SMTP Settings, here you will find the server name and Port as well as be able to create SMTP Credentials.

You should also be sure that your Default mail account which is used for bounce processing (Mail Accounts (Civicrm->Administer CiviCRM->Mail Accounts)) is configured and uses a domain that is verified by SES for your account. Similarly, check that your from email address (Civicrm->Administer CiviCRM->From Email Address Options) is configured and approved in SES.

### Settings for this extension on your site

Drupal Ex: http://{yourDomain}/civicrm/airmail/settings
Wordpress Ex: http://{yourDomain}/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fairmail%2Fsettings

Enter a secret code, it does not matter what this code is should be a random string of numbers and letters.
For Open/Click Processing slect "CiviMail"
For External SMTP Service Select "Amazon SES"

Save Configuration

On save the post URL should display below in a green box it will look something like this:

Wordpress Ex: http://{yourDomain}/civicrm?page=CiviCRM&q=civicrm/airmail/webhook&reset=1&secretcode={secretCode}

### Settings in SES

In SES you will need to:

+ configure SMTP Credentials,
+ verify domains and email addresses.
+ Set up a configuration set by navigating to SES Home -> Configuratin Sets -> Create Configuration Set
  - for destination <Select a destination type> select SNS
  - Configure the SNS destination
    - For Event Types check Bounce and Compliant
    - For Topic create a new topic

### Settings in SNS

Once you have set up a configuration Set you will need to go to SNS home and configure SNS to process the topic.

Go to SNS Home -> Topics -> check the topic you just created and from the Actions list select "Subscribe to topic"

For Create a subscription the Topic ARN should be populates, The Protocol should be HTTP and the endpoint should be the url in the green box on the settings page for this extension (somethng like: http://{yourDomain}/civicrm?page=CiviCRM&q=civicrm/airmail/webhook&reset=1&secretcode={secretCode}) then click "Create Subscription"

At first the Subscription will show up as pendingConfirmation, you may need to wait a minute and then refresh the page before it confirms... SNS sends a subscribe URL to your site which this extension responds to.. that process takes a minute or two.

THINGS TO DO:

+ Deal with all Event Types?
+ When Event Type is Complaint, opt out the contact from all emails
+ Save the arn to civi when there is a successful subscription
