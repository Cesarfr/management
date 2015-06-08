<?php

/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

include_once 'directory.php';

$pageTitle='Administration';
include 'templates/header.php';

//set referring page
$_SESSION['ref_script']=$currentPage;

if ($user->isAdmin()){

?>


<table class="headerTable">
<tr><td>
<span class="headerText">Users</span>&nbsp;&nbsp;<span id='span_User_response' class='redText'></span>
<br /><span id='span_newUser' class='adminAddInput'><a href='ajax_forms.php?action=getAdminUserUpdateForm&height=202&width=288&modal=true' class='thickbox' id='expression'>add new user</a></span>
<br /><br />
<div id='div_User'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText">Document Types</span>&nbsp;&nbsp;<span id='span_DocumentType_response'></span>
<br /><span id='span_newDocumentType' class='adminAddInput'><a href='javascript:showAdd("DocumentType");'>add new document type</a></span>
<br /><br />
<div id='div_DocumentType'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText">Note Types</span>&nbsp;&nbsp;<span id='span_DocumentNoteType_response'></span>
<br /><span id='span_newDocumentNoteType' class='adminAddInput'><a href='javascript:showAdd("DocumentNoteType");'>add new note type</a></span>
<br /><br />
<div id='div_DocumentNoteType'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<?php

$config = new Configuration;

//if the org module is not installed, display provider list for updates
if ($config->settings->organizationsModule != 'Y'){ ?>


	<br />
	<br />

	<table class="headerTable">
	<tr><td>
	<span class="headerText">Categories</span>&nbsp;&nbsp;<span id='span_Consortium_response'></span>
	<br /><span id='span_newConsortium' class='adminAddInput'><a href='javascript:showAdd("Consortium");'>add new category</a></span>
	<br /><br />
	<div id='div_Consortium'>
	<img src = "images/circle.gif">Loading...
	</div>
	</td></tr>
	</table>

	<br />
	<br />
<?php } ?>

<br />

<script type="text/javascript" src="js/admin.js"></script>

<?php
}else{
	echo "You don't have permission to access this page";
}

include 'templates/footer.php';
?>

