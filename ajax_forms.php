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
**************************************************************************************************************************
** ajax_forms.php contains all forms that are displayed using thickbox
**
** when ajax_forms.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
** each form should have a corresponding javascript file located in /js/forms/
**************************************************************************************************************************
*/

include_once 'directory.php';
include_once 'user.php';


switch ($_GET['action']) {

	//form to edit license record
    case 'getLicenseForm':
		if (isset($_GET['licenseID'])) {
			$licenseID = $_GET['licenseID']; 
		} else {
			$licenseID = '';
		}
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		if ($licenseID) {
			$organizationName = $license->getOrganizationName;
		} else {
			$organizationName = '';
		}
		//a new note can be added along with the initial document creation, but not when we're editing a document
		if (!$licenseID) {
	 		$note = new DocumentNote(new NamedArguments(array('primaryKeyName'=>'documentNoteID')));
	 		$documentNoteType = new DocumentNoteType(new NamedArguments(array('primaryKeyName'=>'documentNoteTypeID')));
		}
?>
		<div id='div_licenseForm'>
			<form id='licenseForm'>
				<input type='hidden' id='editLicenseID' name='editLicenseID' value='<?php echo $licenseID; ?>'>
				<input type='hidden' id='editLicenseForm' name='editLicenseForm' value='Y'>
				<table class="thickboxTable" style="width:300px;">
					<tr>
						<td colspan='2'>
							<span id='headerText' class='headerText'><?php if ($licenseID) echo "Edit "; else echo "New " ?>Document</span><br />
						</td>
					</tr>
			
					<tr>
						<td colspan='2'>
							<label for="shortName" class="formText">Name:</label>
							<span id='span_error_licenseShortName' class='errorText'></span><br />
							<input type='textbox' id = 'licenseShortName' value="<?php echo $license->shortName; ?>">
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<label for="description" class="formText">Description:</label>
							<span id='span_error_licenseDescription' class='errorText'></span><br />
							<textarea name='licenseDescription' id = 'licenseDescription' cols='38' rows='2'><?php echo $license->description; ?></textarea>
						</td>
					</tr>
					<input type='hidden' id='licenseOrganizationID' name='licenseOrganizationID' value='<?php echo '0'; ?>'>		
					<input type='hidden' id='organizationName' name='organizationName' value='<?php echo 'Default Internal'; ?>'>		

<?php 
		//if not editing
		if (!$licenseID){
?>		
					<tr>
						<td colspan='2'>
							<label for="documentType" class="formText">Type:</label><br />
							<span id='span_error_documentTypeID' class='errorText'></span>
							<span id='span_documentType'>
								<select name='docTypeID' id='docTypeID' style='width:185px;'>
		<?php
		$display = array();
		$documentType = new DocumentType();

		foreach($documentType->allAsArray() as $display) {
			if ($license->typeID == $display['documentTypeID']){
				echo "				<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "				<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}
?>
								</select>
							</span>
							<br />
							<span id='span_newDocumentType'><a href="javascript:newDocumentType();">add type</a></span>
							<br />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="revisionDate" class="formText">Last Document Revision:</label>
							<div><input class="date-pick" type='input' id='revisionDate' name='revisionDate' value="<?php echo date("m/d/Y");?>" /></div>
						</td>
					</tr>
<?php 
		//if editing
		} else {
?>
					<input type='hidden' id='docTypeID' name='docTypeID' value='<?php echo $license->typeID; ?>'>
<?php		
		}
?>		
		
					<tr>
						<td colspan='2'>
							<label for="consortiumID" class="formText">Categories:</label>
							<span id='span_error_licenseConsortiumID' class='errorText'></span><br />
							<span id='span_consortium'>
<?php
		try{
			$consortiaArray = array();
			$consortiaArray=$license->getConsortiumList()

?>
								<select name='licenseConsortiumID' id='licenseConsortiumID' multiple='multiple'>
<?php
			$display = array();

			$licenseconsortiumids = $license->getConsortiumsByLicense();

			foreach($consortiaArray as $display) {
				if (is_array($licenseconsortiumids) && in_array($display['consortiumID'],$licenseconsortiumids)) {
					echo "			<option value='" . $display['consortiumID'] . "' selected>" . $display['name'] . "</option>";
				}else{
					echo "			<option value='" . $display['consortiumID'] . "'>" . $display['name'] . "</option>";
				}
			}

?>
								</select>
<?php
		}catch(Exception $e){
			echo "				<span style='color:red'>There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.</span>";
		}
?>
							</span>

<?php
		$config = new Configuration;

		//if the org module is not installed allow to add consortium from this screen
		if (($config->settings->organizationsModule == 'N') || (!$config->settings->organizationsModule)){
?>
							<br />
							<span id='span_newConsortium'><a href="javascript:newConsortium();">add category</a></span>
<?php 	
		} 
?>

						</td>
					</tr>	
<?php
		//if editing
		if ($licenseID) {
			// No Editing of file from Main page
			//echo "<div id='div_uploadFile'>" . $document->documentURL . "<br /><a href='javascript:replaceFile();'>replace with new file</a>";
			echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></div>";
		} else {
?>
					<tr>
						<td colspan="2">
							<label for="uploadDocument" class="formText">File:</label>
<?php
			echo "			<div style=\"display:inline;\" id='div_uploadFile'><input type='file' name='upload_button' id='upload_button'></div>";
	}		
?>
							<span id='div_file_message'></span>
							<span id='span_error_licenseuploadDocument' class='errorText'></span>
						</td>
					</tr>
					<tr>
						<td><label for="archiveInd" class="formText">Archived:</label></td>
						<td><input type='checkbox' id='archiveInd' name='archiveInd' value='1' /></td>
					</tr>
<?php
		//only show the new note option if we're creating a new document
		if (!$licenseID) {
?>
					<tr>
						<td colspan="2">
							<a href="#addNote" class="sectiontoggle">Add Optional Note</a>
							<div id="addNote" class="hidden">
								<table style="width:300px;">
									<tr>
										<td colspan='2'>
											<span id='span_errors'></span><br />
										</td>
									</tr>
									<tr>
										<td colspan='2'>
											<label for="note[body]" class="formText">Note:</label><br /><textarea name='note[body]' id = 'noteBody' cols='44' rows='10'></textarea>
										</td>
									</tr>
									<tr>
										<td colspan='2'>
											<label for="note[documentNoteTypeID]" class="formText">Note Type:</label><br />
											<span id='span_noteType'>
<?php
				echo '						<select id="noteDocumentNoteTypeID" name="note[documentNoteTypeID]">';
				foreach($documentNoteType->allAsArray() as $display) {
					echo "						<option value='" . $display['documentNoteTypeID'] . "'>" . $display['shortName'] . "</option>";
				}

				echo '						</select>';
?>
											</span>
											<br />
											<span id='span_newNoteType'><a href="javascript:newNoteType();">add note type</a></span>
											<br />
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
<?php
		}
?>
				</table>
				<table style="width:300px;">
					<tr style="vertical-align:middle;">
						<td style="padding-top:8px;"><input type='button' value='submit' name='submitLicense' id ='submitLicense'></td>
						<td style="padding-top:8px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="tb_remove()"></td>
					</tr>
				</table>
		
				<script type="text/javascript" src="js/forms/licenseForm.js?random=<?php echo rand(); ?>"></script>
			</form>
		</div>
<?php
	break;
	//form to edit/upload documents
    case 'getUploadDocument':

		//document ID passed in for updates only
		if (isset($_GET['documentID'])) $documentID = $_GET['documentID']; else $documentID = '';
		$licenseID = $_GET['licenseID'];

		$document = new Document(new NamedArguments(array('primaryKey' => $documentID)));
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		
		if (count($license->getDocumentsWithoutParents('documentID',$documentID)) > 0) {
			$blockArchiveCheck = 'disabled';
		} else {
			$blockArchiveCheck = '';
		}

		//if effective date isn't set, set it to today's date
		if (($document->effectiveDate == "0000-00-00") || ($document->effectiveDate == "")){
			$effectiveDate = date("m/d/Y");
		}else{
			$effectiveDate=format_date($document->effectiveDate);
		}
		//if revision date isn't set, set it to today's date
		if (($document->revisionDate == "0000-00-00") || ($document->revisionDate == "")){
			$revisionDate = date("m/d/Y");
		} else {
			$revisionDate = format_date($document->revisionDate);
		}

		if (($document->expirationDate) && ($document->expirationDate != '0000-00-00')){
			$archiveChecked = 'checked';
		}else{
			$archiveChecked = '';
		}

 		?>
		<div id='div_uploadDoc'>
		<form id="uploadDoc" action="ajax_processing.php?action=submitDocument" method="POST" enctype="multipart/form-data">
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $licenseID; ?>'>
		<input type='hidden' id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		<table class="thickboxTable" style="width:310px;">
		<tr>
		<td colspan='2'><span class='headerText'>Document Upload</span><br /><span id='span_errors'></span><br /></td>
		</tr>
		<tr>
			<td style='text-align:right;vertical-align:top;'><label for="revisionDate" class="formText">Last Document Revision:</label><br /><span id='span_error_revisionDate' class='errorText'></span></td>
			<td>
				<input type='hidden' id="effectiveDate" name='effectiveDate' value='<?php echo $effectiveDate; ?>' />
				<input class='date-pick' id='revisionDate' name='revisionDate' style='width:80px' value='<?php echo $revisionDate; ?>' />
			</td>
		</tr>

		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="documentType" class="formText">Document Type:</label><br /><span id='span_error_documentTypeID' class='errorText'></span></td>
		<td>
		<span id='span_documentType'>
		<select name='docTypeID' id='docTypeID' style='width:185px;'>
		<?php

		$display = array();
		$documentType = new DocumentType();

		foreach($documentType->allAsArray() as $display) {
			if ($document->documentTypeID == $display['documentTypeID']){
				echo "<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		</span>
		<br />
		<span id='span_newDocumentType'><a href="javascript:newDocumentType();">add document type</a></span>
		<br />
		</td>
		</tr>
		
		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="shortName" class="formText">Name:</label><br /><span id='span_error_shortName' class='errorText'></span></td>
		<td>
		<textarea name='shortName' id = 'shortName' cols='28' rows='2' style='width:185px;'><?php echo $document->shortName; ?></textarea>
		</td>
		</tr>
		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="uploadDocument" class="formText">File:</label></td>
		<td>
		<?php

		//if editing
		if ($documentID){
			echo "<div id='div_uploadFile'>" . $document->documentURL . "<br /><a href='javascript:replaceFile();'>replace with new file</a>";
			echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></div>";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_button' id='upload_button'></div>";
		}


		?>
		<span id='div_file_message'></span>
		</td>
		</tr>

		<?php if (($document->parentDocumentID == "0") || ($document->parentDocumentID == "")){ ?>
		<tr>
			<td style='text-align:right;vertical-align:top;'><label for="archiveInd" class="formText">Archived:</label></td>
			<td>
<?php
if ($_GET['isArchived'] == 1) {
?>
				<input type='checkbox' name='archiveDummy' checked="checked" disabled="disabled" />
				<input type="hidden" id="archiveInd" name="archiveInd" value="1" />
<?php
} else {
?>
				<input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> <?php echo $blockArchiveCheck; ?> />
<?php
}
?>
			</td>
		</tr>
		<?php } ?>

		<tr style="vertical-align:middle;">
		<td style="padding-left:8px;padding-top:8px;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;"><input type='button' value='submit' name='submitDocument' id='submitDocument'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='cancel' onclick="tb_remove()"></td>
		</tr>
		</table>
		</div>

		<script type="text/javascript" src="js/forms/documentForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;





	//form to prompt for date for archiving documents
	//Jan 2010, form no longer used, archive checkbox on document form instead
	//leaving in in case we revert
    case 'getArchiveDocumentForm':

		if (isset($_GET['documentID'])) $documentID = $_GET['documentID']; else $documentID = '';

		?>
		<div id='div_archiveDocumentForm'>
		<table class="thickboxTable" style="width:200px;">
		<tr>
		<td><span class='headerText'>Archive Document Date</span><br /><br /><span id='span_errors'></span></td>
		</tr>
		<tr>
		<td>
		<input type='hidden' name='documentID' id='documentID' value='<?php echo $documentID; ?>' />
		Archive Date:  <input class='date-pick' id='expirationDate' name='expirationDate' style='width:80px' value='<?php echo format_date(date); ?>' />
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='javascript:void(0)' name='submitArchive' id='submitArchive'>Continue</a></td></tr>
		</table>


		<script type="text/javascript" src="js/forms/documentArchiveForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;




	//form to add/edit sfx or other terms tool provider links
    case 'getSFXForm':

		//sfx provider id passed in for updates
		$licenseID = $_GET['licenseID'];
		if (isset($_GET['providerID'])) $sfxProviderID = $_GET['providerID']; else $sfxProviderID = '';

		$sfxProvider = new SFXProvider(new NamedArguments(array('primaryKey' => $sfxProviderID)));
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		?>
		<div id='div_sfxForm'>
		<input type='hidden' id='sfxProviderID' name='sfxProviderID' value='<?php echo $sfxProviderID; ?>'>

		<table class="thickboxTable" style="width:240px;">
		<tr>
		<td colspan='2'><span class='headerText'>Terms Tool Resource Link</span><br /><span id='span_errors'></span><br /></td>
		</tr>


		<tr>
		<td colspan='2'><label for="documentID" class="formText">For Document:</label>  <span id='span_error_documentID' class='errorText'></span><br />
		<select name='documentID' id='documentID' style='width:200px;'>
		<option value=''></option>
		<?php

		$display = array();

		foreach($license->getDocuments() as $display) {
			if ($sfxProvider->documentID == $display->documentID) {
				echo "<option value='" . $display->documentID . "' selected>" . $display->shortName . "</option>";
			}else{
				echo "<option value='" . $display->documentID . "'>" . $display->shortName . "</option>";
			}
		}


		?>
		</select>
		</td>
		</tr>

		<tr>
		<td>
		<label for="shortName" class="formText">Terms Tool Resource:</label>  <span id='span_error_shortName' class='errorText'></span><br />
		<input id='shortName' name='shortName' style='width:190px' value='<?php echo $sfxProvider->shortName; ?>' />
		</td>
		</tr>
		<tr>
		<td style="padding-top:8px;"><input type='button' value='submit' name='submitSFX' id='submitSFX'></td>
		<td style="padding-top:8px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="window.parent.tb_remove()"></td>
		</tr>

		</table>


		<script type="text/javascript" src="js/forms/sfxForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;

	//form to add/edit attachment form
    case 'getAttachmentForm':

		//attachment ID sent in for updates
		if (isset($_GET['attachmentID'])) $attachmentID = $_GET['attachmentID']; else $attachmentID = '';

		$attachment = new Attachment(new NamedArguments(array('primaryKey' => $attachmentID)));

		if (($attachment->sentDate != '') && ($attachment->sentDate != "0000-00-00")) {
			$sentDate = format_date($attachment->sentDate);
		}else{
			$sentDate='';
		}


		?>
		<div id='div_attachmentForm'>
		<form id='attachmentForm'>
		<input type='hidden' id='attachmentID' name='attachmentID' value='<?php echo $attachmentID; ?>'>
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $_GET['licenseID']; ?>'>
		<table class="thickboxTable" style="width:300px;">
		<tr>
		<td colspan='2'><span class='headerText'>Attachments</span><br /><span id='span_errors'></span><br /></td>
		</tr>

		<tr>
		<td colspan='2'><label for="sentDate" class="formText">Date:</label><br />

		<input class='date-pick' id='sentDate' name='sentDate' style='width:80px' value='<?php echo $sentDate; ?>' />

		</tr>

		<tr>
		<td colspan='2'><label for="attachmentText" class="formText">Details:</label><br /><textarea name='attachmentText' id = 'attachmentText' cols='45' rows='10'><?php echo $attachment->attachmentText; ?></textarea></td>

		</tr>
		<tr>
		<td colspan='2' style="width:300px;"><label for="upload_attachment_button" class="formText">Attachments:</label><span id='div_file_message'></span>
		<br /><span id='div_file_success'></span>
		<?php

		//if editing
		if ($attachmentID){
			$attachmentFile = new AttachmentFile();

			foreach ($attachment->getAttachmentFiles() as $attachmentFile){
				echo "<div id='div_existing_" . $attachmentFile->attachmentFileID . "'>" . $attachmentFile->attachmentURL . "  <a href='javascript:removeExistingAttachment(\"" . $attachmentFile->attachmentFileID . "\");' class='smallLink'>remove</a><br /></div>";
			}

			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";
		}


		?>
		</td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="padding-top:8px;"><input type='button' value='submit' name='submitAttachment' id='submitAttachment'></td>
		<td style="padding-top:8px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="tb_remove();window.parent.updateAttachments();"></td>
		</tr>
		</table>



		<script type="text/javascript" src="js/forms/attachmentForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;

	//form to add/edit notes
    case 'getNoteForm':
		//note ID sent in for updates
		if (isset($_GET['documentNoteID'])) {
			 $documentNoteID = $_GET['documentNoteID']; 
		} else {
			 $documentNoteID = '';
		}

		$note = new DocumentNote(new NamedArguments(array('primaryKey' => $documentNoteID)));
		$documentNoteType = new DocumentNoteType(new NamedArguments(array('primaryKeyName'=>'documentNoteTypeID')));
		$license = new License(new NamedArguments(array('primaryKey'=>$_GET['licenseID'])));
		$documents = $license->getAllDocumentNamesAsIndexedArray();
		?>
		<div id='div_noteForm'>
		<form id='noteForm'>
		<input type='hidden' id='documentNoteID' name='documentNoteID' value='<?php echo $documentNoteID; ?>'>
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $_GET['licenseID']; ?>'>
		<table class="thickboxTable" style="width:300px;">
			<tr>
				<td colspan='2'><span class='headerText'>Notes</span><br /><span id='span_errors'></span><br /></td>
			</tr>
			<tr>
				<td colspan='2'><label for="notebody" class="formText">Note:</label><br /><textarea name='notebody' id = 'notebody' cols='44' rows='10'><?php echo $note->body; ?></textarea></td>
			</tr>
			<tr>
				<td colspan='2'>
					<label for="documentNoteTypeID" class="formText">Note Type:</label><br />
					<span id='span_noteType'>

<?php
		echo '			<select id="documentNoteTypeID" name="documentNoteTypeID">';
		foreach($documentNoteType->allAsArray() as $display) {
			if ($note->documentNoteTypeID == $display['documentNoteTypeID']){
				echo "		<option value='" . $display['documentNoteTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "		<option value='" . $display['documentNoteTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}
		echo '			</select>';
?>
					</span>
					<br />
					<span id='span_newNoteType'><a href="javascript:newNoteType();">add note type</a></span>
					<br />

				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<label for="documentID" class="formText">Document:</label><br />
<?php
		echo '		<select id="documentID" name="documentID">
						<option value="0">All Documents</option>';
		foreach($documents as $display) {
			if ($note->documentID == $display['documentID']){
				echo "	<option value='" . $display['documentID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "	<option value='" . $display['documentID'] . "'>" . $display['shortName'] . "</option>";
			}
		}
		echo '		</select>';
?>
				</td>
			</tr>
			<tr style="vertical-align:middle;">
				<td style="padding-top:8px;"><input type='button' value='submit' name='submitNote' id='submitNote' /></td>
				<td style="padding-top:8px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="tb_remove();window.parent.updateNotes();"></td>
			</tr>
		</table>



		<script type="text/javascript" src="js/forms/noteForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;


	//generic form for administering lookup tables on the admin page (these tables simply have an ID and shortName attributes)
	case 'getAdminUpdateForm':
		$updateID = $_GET['updateID'];


		$className = $_GET['tableName'];
		$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));

		?>
		<div id='div_updateForm'>
		<table class="thickboxTable" style="width:200px;">
		<tr>
		<td colspan='2'><br /><span class='headerText'>Update</span><br /><span id='span_errors' style='color:#F00;'></span><br /></td>
		</tr>
		<tr>
		<td>
		<?php
		echo "<input type='text' id='updateVal' name='updateVal' value='" . $instance->shortName . "' style='width:190px;'/></td><td><a href='javascript:updateData(\"" . $className . "\", \"" . $updateID . "\");'>update</a>";
		?>


		</td>
		</tr>
		<tr>
		<td colspan='2'><p><a href='#' onclick='window.parent.tb_remove(); return false'>close</a></td>
		</tr>
		</table>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#updateVal').keyup(function(e) {

				   if(e.keyCode == 13) {
					   updateData("<?php echo $className; ?>", "<?php echo $updateID; ?>");
				   }
        	});

        </script>


		<?php

		break;



	//user form on the admin tab needs its own form since there are other attributes
	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID != ''){
			$update='Update';
			$updateUser = new User(new NamedArguments(array('primaryKey' => $loginID)));
		}else{
			$update='Add New';
		}

		$util = new Utility();

		?>
		<div id='div_updateForm'>
		<table class="thickboxTable" style="width:285px;padding:2px;">
		<tr><td colspan='3'><span class='headerText'><?php echo $update; ?> User</span><br /><span id='span_errors' style='color:#F00;'></span><br /></td></tr>
            <tr><td colspan='2' style='width:135px;'><label for='loginID'><b>Login ID</b></label></td><td><input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' style='width:140px;' /></td></tr>
            <tr><td colspan='2'><label for='firstName'><b>First Name</b></label></td><td><input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>" style='width:140px;' /></td></tr>
            <tr><td colspan='2'><label for='lastName'><b>Last Name</b></label></td><td><input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" style='width:140px;' /></td></tr>
            <tr><td><label for='privilegeID'><b>Privilege</b></label></td>
		<td>
				<fieldset id="fieldsetPrivilege">
				<a title = "Add/Edit users can add, edit, or remove licenses and associated fields<br /><br />Admin users have access to the Admin page and the SFX tab.<br /><br />View only users can view all license information, including the license pdf" href=""><img src='images/help.gif'></a>
				</fieldset>

				<div id="footnote_priv" style='display:none;'>Add/Edit users can add, edit, or remove licenses and associated fields<br /><br />Admin users have access to the Admin page and the SFX tab.<br /><br />View only users can view all license information, including the license pdf</div>

		</td>
		<td>
		<select name='privilegeID' id='privilegeID' style='width:145px'>
		<?php



		$display = array();
		$privilege = new Privilege();

		foreach($privilege->allAsArray() as $display) {
			if ($updateUser->privilegeID == $display['privilegeID']){
				echo "<option value='" . $display['privilegeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['privilegeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		</td>
		</tr>

		<?php
		//if not configured to use SFX, hide the Terms Tool Report
		if ($util->useTermsTool()) {
		?>
            <tr><td><label for='emailAddressForTermsTool'><b>Terms Tool Email</b></label></td>
		<td>
				<fieldset id="fieldsetEmail">
				<a title = "Enter email address if you wish this user to receive email notifications when the terms tool box is checked on the Expressions tab.<br /><br />Leave this field blank if the user shouldn't receive emails." href=""><img src='images/help.gif'></a>
				</fieldset>

		</td>
		<td><input type='text' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='<?php if (isset($updateUser)) echo $updateUser->emailAddressForTermsTool; ?>' style='width:140px;' /></td>
		</tr>

		<?php } else { echo "<input type='hidden' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='' /><br />"; }?>

		<tr style="vertical-align:middle;">
		<td colspan='2' style="padding-top:8px;text-align:right;">&nbsp;</td>
		<td style="padding-top:18px;padding-right:8px;text-align:left;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='cancel' onclick="window.parent.tb_remove(); return false"></td>
		</tr>

		</table>

		</div>

		<script type="text/javascript" src="js/forms/adminUserForm.js?random=<?php echo rand(); ?>"></script>
		<?php

		break;


	//qualifier on admin.php screen - since qualifiers also have expression types
	case 'getQualifierForm':
		if (isset($_GET['qualifierID'])) $qualifierID = $_GET['qualifierID']; else $qualifierID = '';

		if ($qualifierID){
			$update='Update';
			$qualifier = new Qualifier(new NamedArguments(array('primaryKey' => $qualifierID)));
		}else{
			$update='Add New';
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' name='qualifierID' id='qualifierID' value='<?php echo $qualifierID; ?>' />
		<table class="thickboxTable" style="width:290px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'><?php echo $update; ?> Qualifier</span><br /><br /></td></tr>

            <tr><td><label for='expressionTypeID'><b>For Expression Type</b></label></td>
		<td>
		<select name='expressionTypeID' id='expressionTypeID' style='width:155px'>
		<?php

		$display = array();
		$expressionType = new ExpressionType();

		foreach($expressionType->allAsArray() as $display) {
			if ($qualifier->expressionTypeID == $display['expressionTypeID']){
				echo "<option value='" . $display['expressionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['expressionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		</td>
		</tr>

            <tr><td><label for='shortName'><b>Qualifier</b></label></td><td><input type='text' id='shortName' name='shortName' value='<?php if (isset($qualifier)) echo $qualifier->shortName; ?>' style='width:150px;'/></td></tr>

		<tr>
		<td style="padding-top:18px;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitQualifier();' id='submitQualifier'></td>
		<td style="padding-top:18px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="window.parent.tb_remove(); return false"></td>
		</tr>
		</table>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#shortName').keyup(function(e) {

				   if(e.keyCode == 13) {
					   submitQualifier();
				   }
        	});

        </script>

		<?php

		break;


	default:
       echo "Action " . $action . " not set up!";
       break;


}



?>
