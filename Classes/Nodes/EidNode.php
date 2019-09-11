<?php

namespace Socialstream\SocialStream\Nodes;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Form\AbstractNode;

/**
 * Script Class for rendering the Table Wizard
 */
class EidNode extends AbstractNode
{
    public function render()
    {
        $http = isset($_SERVER['HTTPS']) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $url = $http . "://" . $host . "/?eID=generate_token&channel=" . $this->data['databaseRow']['uid'];
        $result['html'] = '
<script>
function copyEidUrl() {
  var copyText = document.getElementById("eid-url");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  alert("Copied the text: " + copyText.value);
}
</script>
<style>
    #eid-url{
        opacity: 0;
    }
    .eid-url:hover, .eid-url:focus, .eid-url:active{
        cursor: pointer;
    }
</style>
<div><span class="eid-url" onclick="copyEidUrl();" >' . $url . '</span><input type="text" value="' . $url . '" id="eid-url"/></div>';
        return $result;
    }

}
