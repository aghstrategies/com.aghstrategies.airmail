# Amazon SES Specific Configuration
## Settings - For CiviMail for Amazon SES

Make sure your Outbound Mail settings are configured to match the settings in your AWS SNS account

Log onto AWS Navigate to the Service SES and then to SMTP Settings, here you will find the server name and Port as well as be able to create SMTP Credentials.

You should also be sure that your Default mail account which is used for bounce processing (Mail Accounts (Civicrm->Administer CiviCRM->Mail Accounts)) is configured and uses a domain that is verified by SES for your account. Similarly, check that your from email address (Civicrm->Administer CiviCRM->From Email Address Options) is configured and approved in SES.

## Settings in Amazon SES

In SES you will need to:

+ configure SMTP Credentials,
+ verify domains and email addresses.
+ Set up a configuration set by navigating to SES Home -> Configuration Sets -> Create Configuration Set
  - for destination {select a destination type} select SNS  
  - Configure the SNS destination  
    - For Event Types check Bounce and Compliant  
    - For Topic create a new topic  

## Settings in SNS
Once you have set up a configuration Set you will need to go to SNS home and configure SNS to process the topic.

Go to SNS Home -> Topics -> check the topic you just created and from the Actions list select "Subscribe to topic"

For Create a subscription the Topic ARN should be populates, The Protocol should be HTTP and the endpoint should be the url in the green box on the settings page for this extension (somethng like: http://{yourDomain}/civicrm?page=CiviCRM&q=civicrm/airmail/webhook&reset=1&secretcode={secretCode}) then click "Create Subscription"

At first the Subscription will show up as pendingConfirmation, you may need to wait a minute and then refresh the page before it confirms... SNS sends a subscribe URL to your site which this extension responds to.. that process takes a minute or two.

## Useful resources
+ Amazon's Testing Email Addresses: http://docs.aws.amazon.com/ses/latest/DeveloperGuide/mailbox-simulator.html
+ Amazon Documentation on SNS messages to HTTP: http://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html
