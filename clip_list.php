<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2023
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	James Rose <james.o.rose@gmail.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('clip_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//include the header
	require_once "header.php";

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//check if this is a file
	function isfile($filename) {
		return @filesize($filename) > 0 ? true : false;
	}

//count the spaces
	function space($count) {
		$r=''; $i=0;
		while($i < $count) {
			$r .= '     ';
			$i++;
		}
		return $r;
	}

//setup ajax
	echo "<script type=\"text/javascript\" language=\"javascript\">\n";
	echo "    function makeRequest(url, strpost) {\n";
	echo "        var http_request = false;\n";
	echo "\n";
	echo "        if (window.XMLHttpRequest) { // Mozilla, Safari, ...\n";
	echo "            http_request = new XMLHttpRequest();\n";
	echo "            if (http_request.overrideMimeType) {\n";
	echo "                http_request.overrideMimeType('text/xml');\n";
	echo "                // See note below about this line\n";
	echo "            }\n";
	echo "        } else if (window.ActiveXObject) { // IE\n";
	echo "            try {\n";
	echo "                http_request = new ActiveXObject(\"Msxml2.XMLHTTP\");\n";
	echo "            } catch (e) {\n";
	echo "                try {\n";
	echo "                    http_request = new ActiveXObject(\"Microsoft.XMLHTTP\");\n";
	echo "                } catch (e) {}\n";
	echo "            }\n";
	echo "        }\n";
	echo "\n";
	echo "        if (!http_request) {\n";
	echo "            alert('".$text['message-give-up']."');\n";
	echo "            return false;\n";
	echo "        }\n";
	echo "        http_request.onreadystatechange = function() { returnContent(http_request); };\n";
	echo "        if (http_request.overrideMimeType) {\n";
	echo "            http_request.overrideMimeType('text/html');\n";
	echo "        }\n";
	echo "        http_request.open('POST', url, true);\n";
	echo "\n";
	echo "\n";
	echo "        if (strpost.length == 0) {\n";
	echo "            //http_request.send(null);\n";
	echo "            http_request.send('name=value&foo=bar');\n";
	echo "        }\n";
	echo "        else {\n";
	echo "            http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');\n";
	echo "            http_request.send(strpost);\n";
	echo "        }\n";
	echo "\n";
	echo "    }\n";
	echo "\n";
	echo "    function returnContent(http_request) {\n";
	echo "\n";
	echo "        if (http_request.readyState == 4) {\n";
	echo "            if (http_request.status == 200) {\n";

	echo "                  parent.editAreaLoader.setValue('edit1', http_request.responseText); \n";
	echo "\n";

	echo "            }\n";
	echo "            else {\n";
	echo "                alert('".$text['message-problem']."');\n";
	echo "            }\n";
	echo "        }\n";
	echo "\n";
	echo "    }\n";
	echo "</script>";

//setup the tree view
	//echo "// ---------------------------------------------\n";
	//echo "// --- http://www.codeproject.com/jscript/dhtml_treeview.asp\n";
	//echo "// --- Name:    Easy DHTML Treeview           --\n";
	//echo "// --- Author:  D.D. de Kerf                  --\n";
	//echo "// --- Version: 0.2          Date: 13-6-2001  --\n";
	//echo "// ---------------------------------------------\n";
	echo "<script language='JavaScript'>\n";
	echo "function Toggle(node) {\n";
	echo "	// Unfold the branch if it isn't visible\n";
	echo "	if (node.nextSibling.style.display == 'none')	{\n";
	echo "  		node.nextSibling.style.display = 'block';\n";
	echo "	}\n";
	echo "	// Collapse the branch if it IS visible\n";
	echo "	else	{\n";
	echo "  		node.nextSibling.style.display = 'none';\n";
	echo "	}\n";
	echo "\n";
	echo "}\n";
	echo "</script>";

// keyboard shortcut bindings
	echo "<script language='JavaScript' type='text/javascript' src='".PROJECT_PATH."/resources/jquery/jquery-3.6.1.min.js'></script>\n";
	echo "<script src='https://code.jquery.com/jquery-migrate-3.1.0.js'></script>\n";

//save file
	key_press('ctrl+s', 'down', 'window', null, null, "if (parent.document.getElementById('frm_edit')) { parent.$('form#frm_edit').submit(); return false; }", true);

//open file manager/clip library pane
	key_press('ctrl+q', 'down', 'window', null, null, "if (parent.document.getElementById('sidebar')) { parent.toggle_sidebar(); parent.focus_editor(); return false; }", true);

//prevent backspace (browser history back)
	key_press('backspace', 'down', 'window', null, null, 'return false;', true);

//keyboard shortcut to execute command (when included on command page)
	key_press('ctrl+enter', 'down', 'window', null, null, "if (!parent.document.getElementById('sidebar')) { parent.$('form#frm').submit(); return false; }", true);

//show the content
	echo "</head>\n";
	echo "<body style='margin: 0; padding: 5px;' onfocus='blur();'>\n";

	echo "<div style='text-align: left; padding-top: 3px;'>\n";
	echo "<div style='padding-bottom: 3px;'><a href='javascript:void(0);' onclick=\"window.open('clip_options.php','clipwin','left=20,top=20,width=310,height=350,toolbar=0,resizable=0');\" style='text-decoration:none; cursor: pointer;' title=\"".$text['label-clip-library']."\"><img src='resources/images/icon_gear.png' border='0' align='absmiddle' style='margin: 0px 2px 4px -1px;'>".$text['label-clip-library']."</a></div>\n";

	$sql = "select * from v_clips order by clip_folder asc, clip_name asc";
	$database = new database;
	$result = $database->select($sql, null, 'all');
	unset($sql);

	if (!empty($result) && is_array($result) && @sizeof($result) != 0) {
		$master_array = array();
		foreach ($result as $row) {
			if (!empty($row['clip_folder'])) {
				$clip_folder = rtrim($row['clip_folder'], '/');
			}
			$clip_folder .= '/'.$row['clip_name'];

			$parts = explode('/', $clip_folder);
			$folders = array();
			while ($bottom = array_pop($parts)) {
				if (sizeof($folders) > 0) {
					$folders = array($bottom => $folders);
				}
				else {
					$clip['uuid'] = $row['clip_uuid'];
					$clip['name'] = $row['clip_name'];
					$clip['before'] = $row['clip_text_start'];
					$clip['after'] = $row['clip_text_end'];
					$folders = array($bottom => $clip);
				}
			}

			$master_array = array_merge_recursive($master_array, $folders);
		}
		unset($result, $row);

		function parse_array($array) {
			if (!empty(is_array($array)) && is_array($array)) {
				foreach ($array as $folder_name => $clips) {
					//folder
					echo "<a onclick='Toggle(this);' style='display: block; cursor: pointer; text-decoration: none;'><img src='resources/images/icon_folder.png' border='none' align='absmiddle' style='margin: 1px 2px 3px 0px;'>".$folder_name."</a>";
					echo "<div style='display: none; padding-left: 16px;'>\n";

					//clips
					foreach($clips as $row) {
						//clip
						echo "<div style='white-space: nowrap;'>\n";
						echo "<a href='javascript:void(0);' onclick=\"parent.insert_clip(document.getElementById('before_".$row['uuid']."').value, document.getElementById('after_".$row['uuid']."').value);\">";
						echo "<img src='resources/images/icon_file.png' border='0' align='absmiddle' style='margin: 1px 2px 3px -1px;'>";
						echo escape($row['name']);
						echo "</a>\n";
						echo "<textarea style='display: none' id='before_".$row['uuid']."'>".$row['before']."</textarea>\n";
						echo "<textarea style='display: none' id='after_".$row['uuid']."'>".$row['after']."</textarea>\n";
						echo "</div>\n";
					}
					
					echo "</div>\n";
				}
			}
		}
		parse_array($master_array);
	}
	echo "</div>\n";

//inclue the footer
require_once "footer.php";
