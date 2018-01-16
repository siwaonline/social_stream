.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _developer:

Developer Corner
================

If you want to add a social media channel of your own, you need following files:

- a FeedUtility
- a TokenUtility
- an icon


**for example we want to add a social media channel `Foo`**

FeedUtility
-----------

add Classes/Utility/Feed/**Foo**Utility.php

- `getChannel`
- `renewToken`
- `getFeed`
- `getCategory`

best practice: copy the FacebookUtility

TokenUtility
------------

add Classes/Utility/Token/**Foo**Utility.php

- `getAccessUrl`
- `retrieveToken`
- `getValues`

best practice: copy the FacebookUtility

BaseUtility
-----------

modify Classes/Utility/BaseUtility.php

function `getTypes` -> add '**foo**' => 'Foo Name'


Icon
----

add Resources/Public/Icons/socialstream_domain_model_channel_**foo**.svg

best practice: copy socialstream_domain_model_channel_empty.svg