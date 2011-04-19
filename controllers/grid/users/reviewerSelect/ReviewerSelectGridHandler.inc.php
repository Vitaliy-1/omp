<?php

/**
 * @file controllers/grid/users/reviewerSelect/ReviewerSelectGridHandler.inc.php
 *
 * Copyright (c) 2000-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ReviewerSelectGridHandler
 * @ingroup controllers_grid_users_reviewerSelect
 *
 * @brief Handle reviewer selector grid requests.
 */

// import grid base classes
import('lib.pkp.classes.controllers.grid.GridHandler');


// import submissionContributor grid specific classes
import('controllers.grid.users.reviewerSelect.ReviewerSelectGridCellProvider');
import('controllers.grid.users.reviewerSelect.ReviewerSelectGridRow');

class ReviewerSelectGridHandler extends GridHandler {
	/**
	 * Constructor
	 */
	function ReviewerSelectGridHandler() {
		parent::GridHandler();

		$this->addRoleAssignment(array(ROLE_ID_SERIES_EDITOR, ROLE_ID_PRESS_MANAGER),
				array('fetchGrid'));
	}

	//
	// Implement template methods from PKPHandler
	//
	/**
	 * @see PKPHandler::authorize()
	 * @param $request PKPRequest
	 * @param $args array
	 * @param $roleAssignments array
	 */
	function authorize(&$request, $args, $roleAssignments) {
		import('classes.security.authorization.OmpWorkflowStageAccessPolicy');
		$this->addPolicy(new OmpWorkflowStageAccessPolicy($request, $args, $roleAssignments, 'monographId', WORKFLOW_STAGE_ID_INTERNAL_REVIEW));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/*
	 * Configure the grid
	 * @param $request PKPRequest
	 */
	function initialize(&$request) {
		parent::initialize($request);

		Locale::requireComponents(array(LOCALE_COMPONENT_PKP_SUBMISSION, LOCALE_COMPONENT_PKP_MANAGER, LOCALE_COMPONENT_PKP_USER, LOCALE_COMPONENT_OMP_EDITOR));
		$monograph =& $this->getAuthorizedContextObject(ASSOC_TYPE_MONOGRAPH);

		// Columns
		$cellProvider = new ReviewerSelectGridCellProvider();
		$this->addColumn(
			new GridColumn(
				'select',
				'',
				null,
				'controllers/grid/users/reviewerSelect/reviewerSelectRadioButton.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'name',
				'author.users.contributor.name',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'done',
				'common.done',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'avg',
				'editor.review.days',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'last',
				'editor.submissions.lastAssigned',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'active',
				'common.active',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
		$this->addColumn(
			new GridColumn(
				'interests',
				'user.interests',
				null,
				'controllers/grid/gridCell.tpl',
				$cellProvider
			)
		);
	}


	//
	// Overridden methods from GridHandler
	//
	/**
	 * @see GridHandler::getRowInstance()
	 * @return ReviewerSelectGridRow
	 */
	function &getRowInstance() {
		$row = new ReviewerSelectGridRow();
		return $row;
	}

	/**
	 * @see GridHandler::renderFilter()
	 */
	function renderFilter($request) {
		return parent::renderFilter($request, $this->_getFilterData());
	}

	/**
	 * @see GridHandler::loadData()
	 */
	function loadData($request, $filter) {
		$interests = $filter['interestSearchKeywords'];
		$reviewerValues = $filter['reviewerValues'];

		// Retrieve the submissionContributors associated with this monograph to be displayed in the grid
		$done_min = $reviewerValues['done_min'];
		$done_max = $reviewerValues['done_max'];
		$avg_min = $reviewerValues['avg_min'];
		$avg_max = $reviewerValues['avg_max'];
		$last_min = $reviewerValues['last_min'];
		$last_max = $reviewerValues['last_max'];
		$active_min = $reviewerValues['active_min'];
		$active_max = $reviewerValues['active_max'];

		$seriesEditorSubmissionDao =& DAORegistry::getDAO('SeriesEditorSubmissionDAO');
		$monograph =& $this->getAuthorizedContextObject(ASSOC_TYPE_MONOGRAPH);
		$data =& $seriesEditorSubmissionDao->getFilteredReviewers($monograph->getPressId(), $done_min, $done_max, $avg_min, $avg_max,
					$last_min, $last_max, $active_min, $active_max, $interests, $monograph->getId(), $monograph->getCurrentRound());
		return $data;
	}

	/**
	 * @see GridHandler::getFilterSelectionData()
	 * @return array Filter selection data.
	 */
	function getFilterSelectionData($request) {
		$form = $this->getFilterForm();

		// Only read form data if the clientSubmit flag has been checked
		$clientSubmit = (boolean) $request->getUserVar('clientSubmit');

		$form->readInputData();
		if($clientSubmit && $form->validate()) {
			return $form->getFilterSelectionData();
		} else {
			// Load defaults
			return $this->_getFilterData();
		}
	}

	/**
	 * @see GridHandler::getFilterForm()
	 * @return Form
	 */
	function getFilterForm() {
		$monograph =& $this->getAuthorizedContextObject(ASSOC_TYPE_MONOGRAPH);
		import('controllers.grid.users.reviewerSelect.form.AdvancedSearchReviewerFilterForm');
		$filterForm = new AdvancedSearchReviewerFilterForm($monograph);
		return $filterForm;
	}

	/**
	 * Get the default filter data for this grid
	 * @return array
	 */
	function _getFilterData() {
		// Get the monograph
		$monograph =& $this->getAuthorizedContextObject(ASSOC_TYPE_MONOGRAPH);
		$filterData = array();

		$interestDao =& DAORegistry::getDAO('InterestDAO');
		$filterData['interestSearchKeywords'] = $interestDao->getAllUniqueInterests();

		$seriesEditorSubmissionDAO =& DAORegistry::getDAO('SeriesEditorSubmissionDAO');
		$reviewerValues = $seriesEditorSubmissionDAO->getAnonymousReviewerStatistics();
		$filterData['reviewerValues'] = $reviewerValues;

		return $filterData;
	}
}

?>
