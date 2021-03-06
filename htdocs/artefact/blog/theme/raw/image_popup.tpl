<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{str tag=insertimage section=artefact.blog}</title>
	<script language="javascript" type="text/javascript" src="{$WWWROOT}js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="{$WWWROOT}js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="{$WWWROOT}js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="{$WWWROOT}artefact/blog/image_popup.js"></script>
	<base target="_self" />
</head>
<body id="image" style="display: none">
<form onsubmit="ImageDialog.update();return false;" action="#">
	<div class="tabs">
		<ul>
			<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{str tag=insertimage section=artefact.blog}</a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper">
		<div id="general_panel" class="panel current">
     <!--input id="src" name="src" type="text" value="" style="width: 200px" onchange="getImageData();" /-->
     <input id="src" name="src" type="hidden" value="" />
     <input id="imgid" name="imgid" type="hidden" value="" />
     <input id="alt" name="alt" type="hidden" value="" />
     <div id="srcbrowsercontainer"></div>
     <table border="0" cellpadding="4" cellspacing="0">
		  <!-- Image list -->
          <tr>
            <td nowrap="nowrap"><label for="align">{str tag=image}</label></td>
            <td id="image_list_container"></td>
          </tr>   
		  <!-- /Image list -->
          <tr>
            <td nowrap="nowrap"><label for="align">{str section=artefact.blog tag=alignment}</label></td>
            <td><select id="align" name="align">
                <option value="">--</option>
                <option value="baseline">{str section=artefact.blog tag=baseline}</option>
                <option value="top">{str section=artefact.blog tag=top}</option>
                <option value="middle">{str section=artefact.blog tag=middle}</option>
                <option value="bottom">{str section=artefact.blog tag=bottom}</option>
                <option value="text-top">{str section=artefact.blog tag=texttop}</option>
                <option value="text-bottom">{str section=artefact.blog tag=textbottom}</option>
                <option value="left">{str section=artefact.blog tag=left}</option>
                <option value="right">{str section=artefact.blog tag=right}</option>
              </select></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="width">{str section=artefact.blog tag=dimensions}</label></td>
            <td><input id="width" name="width" type="text" value="" size="3" maxlength="5" />
              x
              <input id="height" name="height" type="text" value="" size="3" maxlength="5" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="border">{str section=artefact.blog tag=border}</label></td>
            <td><input id="border" name="border" type="text" value="" size="3" maxlength="3" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="vspace">{str section=artefact.blog tag=verticalspace}</label></td>
            <td><input id="vspace" name="vspace" type="text" value="" size="3" maxlength="3" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="hspace">{str section=artefact.blog tag=horizontalspace}</label></td>
            <td><input id="hspace" name="hspace" type="text" value="" size="3" maxlength="3" /></td>
          </tr>
        </table>
		</div>
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="{str section=artefact.blog tag=insert}" onclick="ImageDialog.update();return false;" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{str section=artefact.blog tag=cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>
</body>
</html>
