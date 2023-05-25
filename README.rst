=============================================================================
SOCIAL STREAM
=============================================================================


A TYPO3 extension to crawl the data, posts, events and images from a Social Media Page and saves them as tx_news records to the database.

The posts are saved as normal news articles from the tx_news extension.

Highly extendable with every Social Media Platform, that provides an API

Provides a scheduler task so the pages are crawled each day.

Issue Tracking at https://github.com/siwaonline/social_stream/issues.

-----------------------------------------------------------------------------

Installation
=============================================================================
Please make sure the PHP Extension bcmath is installed. If you are using a docker
environment this can be done with the following command run inside of the container

``
docker-php-ext-install bcmath
``
-----------------------------------------------------------------------------

Configuration
=============================================================================


Please include the Plugin in you Main Template and then change to the Constant Editor.

Select PLUGIN.TX_SOCIALSTREAM_PI1

The plugin requires your storage PID, app ID and app secret.

You can get an app ID and app secret when you create a new facebook app at https://developers.facebook.com/.

Create a facebook app
-----------------------------------------------------------------------------


Firstly you need to register with your facebook account.

You are now registered as a facebook developer, that means you can now create facebook apps.

Add a new app via the "My Apps" button on the top right.

Choose a Website App and skip the quickstart (top right in the picture).

Now you have a facebook app and can copy the **App Id** and the **App Secret** into the Constants of the ``Social Stream`` Plugin.

-----------------------------------------------------------------------------



Add a channel
=============================================================================


Go to the List View ov you storage Folder and create a new Social Stream Channel.

Select your Type, enter your Object ID and save.

If you want to access a person - not a page - enter ``me``.

After you saved, you have to get your Access Token - click on the button.

The PopUp redirects you to your Social Media Page - you have to log in and accept the plugin.

Redirect Path: /_assets/b8acd56a12fa390388e961cc3b76df2c/Redirect.php

Use the eID Controller for external persons
-----------------------------------------------------------------------------


Just call ``/?eID=generate_token&channel=5`` (replace 5 with the ID of the channel) or send the link to the owner of the Facebook Page.

Only works for Facebook yet.



Get the name or id of your facebook page
-----------------------------------------------------------------------------


For example:

If your facebook page URL is https://www.facebook.com/siwa.online/?fref=ts then your name is ``siwa.online``

You can look your page ID up on this site: https://findmyfbid.com/

-----------------------------------------------------------------------------



Use the scheduler to crawl for posts once a day
=============================================================================


Menu **Scheduled tasks**

Create a new task with the class  ``extbase`` - ``Extbase CommandController Task`` and enter the rootPage ID if your Root Page hasn't the ID 1

Type ``Recurring``, Frequency ``0 0 * * *``

Menu **setup check**

The first rootpage must have an storagePid set in the constants, otherwise the CommandController can't find the entries!

Copy the script line ``<path-to-your-typo3>/typo3/sysext/core/bin/typo3 scheduler:run``

Enter this line in your /etc/crontab file.

I would recommend 0/5 * * * * so your scheduler is called every 5 minutes.

-----------------------------------------------------------------------------



NEWS
=============================================================================


You will now have many tx_news records in your storage folder also categorized.

The templating is now up to you.

You can use the standard news template or design your own one.

