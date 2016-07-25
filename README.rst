=============================================================================
Facebook Stream
=============================================================================


A TYPO3 extension to crawl the data, posts and images from a Facebook Page and saves it to the Database.

The posts are used like news articles from the news extension.

It provides different views for the list/show action (article styled, list styled).

Saves the Images as FAL, so you can also set titles, alternatives, ...

Provides a scheduler task so the pages are crawled each day.

Issue Tracking at https://forge.typo3.org/projects/extension-social_stream.

-----------------------------------------------------------------------------

Configuration
=============================================================================


Please include the Plugin in you Main Template and then change to the Constant Editor.

Select PLUGIN.TX_FACEBOOKSTREAM_PI1

The plugin requires your storage PID, app ID and app secret.

You can get an app ID and app secret when you create a new facebook app at https://developers.facebook.com/.

Create a facebook app
-----------------------------------------------------------------------------


Firstly you need to register with your facebook account.

You are now registered as a facebook developer, that means you can now create facebook apps.

Add a new app via the "My Apps" button on the top right.

Choose a Website App and skip the quickstart (top right in the picture).

Now you have a facebook app and can copy the **App Id** and the **App Secret** into the Constants of the ``Facebook Stream`` Plugin.

-----------------------------------------------------------------------------



Backend Plugin
=============================================================================


Create and import a facebook page by inserting its name or id in the insert field.

Below are all created facebook page listed, you have created already.

Get the name or id of your facebook page
-----------------------------------------------------------------------------


For example:

If your facebook page URL is https://www.facebook.com/siwa.online/?fref=ts then your name is ``siwa.online``

If your facebook page URL is https://www.facebook.com/ADhouse-Communication-Group-121064187970420/?fref=ts then your id is ``121064187970420``

-----------------------------------------------------------------------------



Frontend Plugin
=============================================================================


Just create a new ``Facebook Stream`` Plugin on the page where the articles should be shown.

**View**

Mixed Post View = get the posts from all your provided facebook pages sorted by date and time

Post Overview = list all provided facebook pages and show their posts in the show view

Mixed Gallery View = get the uploaded images from all your provided facebook pages sorted by date and time

Gallery Overview = list all provided facebook pages and show their uploaded images in the show view

**Facebook Pages**

Select the facebook pages you want to show from the ones you crawled in the backend plugin

**max entries to show**

maximum entries (posts or images) you want to show

**use pagination**

if you want to use a pagination

**max entries per page**

how many entries you want to show per pagination page

**open link with**

facebook = link to the facebook post

single show view = show the post on an own site

**style to use**

article style = whole width, styled as articles

list style = max. 500px width, styled as scrollable list

-----------------------------------------------------------------------------



Real Url
=============================================================================


You can use this realurl conf for your site:

                'social_stream' => array(
                    array(
                        'GETvar' => 'tx_socialstream_pi1[action]',
                    ),
                    array(
                        'GETvar' => 'tx_socialstream_pi1[controller]',
                    ),
                ),
                'social_stream_post' => array(
                    array(
                        'GETvar' => 'tx_socialstream_pi1[post]',
                        'lookUpTable' => array(
                            'table' => 'tx_socialstream_domain_model_post',
                            'id_field' => 'uid',
                            'alias_field' => 'object_id',
                            'addWhereClause' => ' AND NOT deleted',
                            'useUniqueCache' => 1,
                            'useUniqueCache_conf' => array(
                                'strtolower' => 1,
                                'spaceCharacter' => '-',
                            ),
                        ),
                    ),
                ),
                'social_stream_page' => array(
                    array(
                        'GETvar' => 'tx_socialstream_pi1[page]',
                        'lookUpTable' => array(
                            'table' => 'tx_socialstream_domain_model_page',
                            'id_field' => 'uid',
                            'alias_field' => 'name',
                            'addWhereClause' => ' AND NOT deleted',
                            'useUniqueCache' => 1,
                            'useUniqueCache_conf' => array(
                                'strtolower' => 1,
                                'spaceCharacter' => '-',
                            ),
                        ),
                    ),
                    array(
                        'GETvar' => 'tx_socialstream_pi1[viewType]',
                    ),
                ),
                'social_stream_site' => array(
                    array(
                        'GETvar' => 'tx_socialstream_pi1[@widget_0][currentPage]',
                    ),
                ),

You can add the following two rules for the encodeSpURL_postProc or encodeSpURL_preProc:

    $params['URL'] = str_replace('social_stream/list/Page/social_stream_page', 'social-stream-list', $params['URL']);
    $params['URL'] = str_replace('social_stream/showSinglePost/Page/social_stream_post', 'social-stream-show', $params['URL']);

    $params['URL'] = str_replace('social-stream-list', 'social_stream/list/Page/social_stream_page', $params['URL']);
    $params['URL'] = str_replace('social-stream-show', 'social_stream/showSinglePost/Page/social_stream_post', $params['URL']);

-----------------------------------------------------------------------------



Use the scheduler to crawl for posts once a day
=============================================================================

Scheduler
-----------------------------------------------------------------------------

Menu **Scheduled tasks**

Create a new task with the class  ``extbase`` - ``Extbase CommandController Task`` and enter the rootPage ID if your Root Page hasn't the ID 1

Type ``Recurring``, Frequency ``100``

Menu **setup check**

If you haven't done yet, create the user ``_cli_scheduler`` and add a **filemount** to fileadmin!!! (otherwise the user has no access to the storage folders)

The first rootpage must have an storagePid set in the constants, otherwise the CommandController can't find the entries!

Copy the script line ``<path-to-your-typo3>/cli_dispatch.phpsh scheduler``

Cronjob
-----------------------------------------------------------------------------

Connect to your server via command line.

``vi /etc/crontab``

i-key

insert the cronjob command ``0 0 * * * root php -f <path-to-your-typo3>/cli_dispatch.phpsh scheduler``

Esc-key

``:wq``

now the scheduler is called every day at midnight