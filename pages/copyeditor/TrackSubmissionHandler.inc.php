<?php

/**
 * TrackSubmissionHandler.inc.php
 *
 * Copyright (c) 2003-2004 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package pages.copyeditor
 *
 * Handle requests for submission tracking. 
 *
 * $Id$
 */

class TrackSubmissionHandler extends CopyeditorHandler {
	
	function submission($args) {
		parent::validate();
		$articleId = $args[0];
		parent::setupTemplate(true, $articleId);
		
		TrackSubmissionHandler::validate($articleId);

		CopyeditorAction::copyeditUnderway($articleId);
		
		$journal = &Request::getJournal();
		$copyeditorSubmissionDao = &DAORegistry::getDAO('CopyeditorSubmissionDAO');
		$submission = $copyeditorSubmissionDao->getCopyeditorSubmission($articleId);
		$useLayoutEditors = $journal->getSetting('useLayoutEditors');
		
		$templateMgr = &TemplateManager::getManager();
		
		$templateMgr->assign('submission', $submission);
		$templateMgr->assign('copyeditor', $submission->getCopyeditor());
		$templateMgr->assign('initialCopyeditFile', $submission->getInitialCopyeditFile());
		$templateMgr->assign('editorAuthorCopyeditFile', $submission->getEditorAuthorCopyeditFile());
		$templateMgr->assign('finalCopyeditFile', $submission->getFinalCopyeditFile());
		$templateMgr->assign('proofAssignment', $submission->getProofAssignment());
		$templateMgr->assign('useLayoutEditors', $useLayoutEditors);
		$templateMgr->assign('helpTopicId', 'editorial.copyeditorsRole.copyediting');
		$templateMgr->display('copyeditor/submission.tpl');
	}
	
	function completeCopyedit($args) {
		parent::validate();
		$articleId = Request::getUserVar('articleId');
		parent::setupTemplate($articleId);
		
		TrackSubmissionHandler::validate($articleId);
		
		if (CopyeditorAction::completeCopyedit($articleId, Request::getUserVar('send'))) {
			Request::redirect(sprintf('copyeditor/submission/%d', $articleId));
		}
	}
	
	function completeFinalCopyedit($args) {
		parent::validate();
		$articleId = Request::getUserVar('articleId');
		parent::setupTemplate(true, $articleId);
		
		TrackSubmissionHandler::validate($articleId);
		
		if (CopyeditorAction::completeFinalCopyedit($articleId, Request::getUserVar('send'))) {
			Request::redirect(sprintf('copyeditor/submission/%d', $articleId));
		}
	}
	
	function uploadCopyeditVersion() {
		$articleId = Request::getUserVar('articleId');
		TrackSubmissionHandler::validate($articleId);
		
		$copyeditStage = Request::getUserVar('copyeditStage');
		CopyeditorAction::uploadCopyeditVersion($articleId, $copyeditStage);
		
		Request::redirect(sprintf('copyeditor/submission/%d', $articleId));	
	}
	
	//
	// Misc
	//
	
	/**
	 * Download a file.
	 * @param $args array ($articleId, $fileId, [$revision])
	 */
	function downloadFile($args) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$fileId = isset($args[1]) ? $args[1] : 0;
		$revision = isset($args[2]) ? $args[2] : null;

		TrackSubmissionHandler::validate($articleId);
		if (!CopyeditorAction::downloadCopyeditorFile($articleId, $fileId, $revision)) {
			Request::redirect(sprintf('%s/submission/%d', Request::getRequestedPage(), $articleId));
		}
	}
	
	/**
	 * View a file (inlines file).
	 * @param $args array ($articleId, $fileId, [$revision])
	 */
	function viewFile($args) {
		$articleId = isset($args[0]) ? $args[0] : 0;
		$fileId = isset($args[1]) ? $args[1] : 0;
		$revision = isset($args[2]) ? $args[2] : null;

		TrackSubmissionHandler::validate($articleId);
		if (!CopyeditorAction::viewFile($articleId, $fileId, $revision)) {
			Request::redirect(sprintf('%s/submission/%d', Request::getRequestedPage(), $articleId));
		}
	}

	//
	// Validation
	//
	
	/**
	 * Validate that the user is the assigned copyeditor for
	 * the article.
	 * Redirects to copyeditor index page if validation fails.
	 */
	function validate($articleId) {
		$copyeditorSubmissionDao = &DAORegistry::getDAO('CopyeditorSubmissionDAO');
		$journal = &Request::getJournal();
		$user = &Request::getUser();
		
		$isValid = true;
		
		$copyeditorSubmission = &$copyeditorSubmissionDao->getCopyeditorSubmission($articleId, $user->getUserId());
		
		if ($copyeditorSubmission == null) {
			$isValid = false;
		} else if ($copyeditorSubmission->getJournalId() != $journal->getJournalId()) {
			$isValid = false;
		} else {
			if ($copyeditorSubmission->getCopyeditorId() != $user->getUserId()) {
				$isValid = false;
			}
		}
		
		if (!$isValid) {
			Request::redirect(Request::getRequestedPage());
		}
	}

	//
	// Proofreading
	//

	/**
	 * Set the author proofreading date completion
	 */
	function authorProofreadingComplete($args) {
		parent::validate();
		$articleId = Request::getUserVar('articleId');
		parent::setupTemplate(true, $articleId);

		$send = false;
		if (isset($args[0])) {
			$send = Request::getUserVar('send') ? true : false;
		}

		TrackSubmissionHandler::validate($articleId);

		if ($send) {
			ProofreaderAction::proofreadEmail($articleId,'PROOFREAD_AUTHOR_COMPLETE');
			Request::redirect(sprintf('copyeditor/submission/%d', $articleId));	
		} else {
			ProofreaderAction::proofreadEmail($articleId,'PROOFREAD_AUTHOR_COMPLETE','/copyeditor/authorProofreadingComplete/send');
		}
	}

	/**
	 * Proof / "preview" a galley.
	 * @param $args array ($articleId, $galleyId)
	 */
	function proofGalley($args) {
		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		TrackSubmissionHandler::validate($articleId);
		
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->display('submission/layout/proofGalley.tpl');
	}
	
	/**
	 * Proof galley (shows frame header).
	 * @param $args array ($articleId, $galleyId)
	 */
	function proofGalleyTop($args) {
		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		TrackSubmissionHandler::validate($articleId);
		
		$templateMgr = &TemplateManager::getManager();
		$templateMgr->assign('articleId', $articleId);
		$templateMgr->assign('galleyId', $galleyId);
		$templateMgr->assign('backHandler', 'submission');
		$templateMgr->display('submission/layout/proofGalleyTop.tpl');
	}
	
	/**
	 * Proof galley (outputs file contents).
	 * @param $args array ($articleId, $galleyId)
	 */
	function proofGalleyFile($args) {
		$articleId = isset($args[0]) ? (int) $args[0] : 0;
		$galleyId = isset($args[1]) ? (int) $args[1] : 0;
		TrackSubmissionHandler::validate($articleId);
		
		$galleyDao = &DAORegistry::getDAO('ArticleGalleyDAO');
		$galley = &$galleyDao->getGalley($galleyId, $articleId);
		
		import('file.ArticleFileManager'); // FIXME
		
		if (isset($galley)) {
			if ($galley->isHTMLGalley()) {
				$templateMgr = &TemplateManager::getManager();
				$templateMgr->assign('galley', $galley);
				$templateMgr->display('submission/layout/proofGalleyHTML.tpl');
				
			} else {
				// View non-HTML file inline
				TrackSubmissionHandler::viewFile(array($articleId, $galley->getFileId()));
			}
		}
	}
	
	/**
	 * Metadata functions.
	 */
	function viewMetadata($args) {
		$articleId = $args[0];

		parent::validate();
		parent::setupTemplate(true, $articleId, 'editing');
	
		TrackSubmissionHandler::validate($articleId);
		CopyeditorAction::viewMetadata($articleId, ROLE_ID_COPYEDITOR);
	}
	
	function saveMetadata() {
		$articleId = Request::getUserVar('articleId');
		
		parent::validate();
		parent::setupTemplate(true, $articleId);
		
		TrackSubmissionHandler::validate($articleId);
		CopyeditorAction::saveMetadata($articleId);
		Request::redirect(Request::getRequestedPage() . "/submission/$articleId");
	}

}
?>
