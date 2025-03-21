/**
 * @file js/load.js
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Compiler entry point for building the JavaScript package. File imports
 *  using the `@` symbol are aliased to `lib/ui-library/src`.
 */

// styles
import '@/styles/_global.less';
import '@/styles/tw-theme-vars.css';

import PkpLoad from '../lib/pkp/js/load.js';

// Import controllers used by OMP
import Container from '@/components/Container/Container.vue';
import AdvancedSearchReviewerContainer from '@/components/Container/AdvancedSearchReviewerContainer.vue';
import Page from '@/components/Container/Page.vue';
import WorkflowPageOMP from '@/pages/workflow/WorkflowPageOMP.vue';

import AccessPage from '@/components/Container/AccessPage.vue';
import AddContextContainer from '@/components/Container/AddContextContainer.vue';
import AdminPage from '@/components/Container/AdminPage.vue';
import DecisionPage from '@/components/Container/DecisionPage.vue';
import DoiPage from '@/components/Container/DoiPageOMP.vue';
import ImportExportPage from '@/components/Container/ImportExportPage.vue';
import ManageEmailsPage from '@/components/Container/ManageEmailsPage.vue';
import SettingsPage from '@/components/Container/SettingsPage.vue';
import StartSubmissionPage from '@/components/Container/StartSubmissionPage.vue';
import StatsEditorialPage from '@/components/Container/StatsEditorialPage.vue';
import StatsPublicationsPage from '@/components/Container/StatsPublicationsPage.vue';
import StatsContextPage from '@/components/Container/StatsContextPage.vue';
import StatsUsersPage from '@/components/Container/StatsUsersPage.vue';
import SubmissionWizardPage from '@/components/Container/SubmissionWizardPageOMP.vue';
import ManageCatalogPage from '@/components/Container/ManageCatalogPage.vue';

// Helper for initializing and tracking Vue controllers
import VueRegistry from '../lib/pkp/js/classes/VueRegistry.js';
VueRegistry.registerComponent('WorkflowPage', WorkflowPageOMP);



// Expose Vue, the registry and controllers in a global var
window.pkp = Object.assign(PkpLoad, window.pkp || {}, {
	controllers: {
		Container,
		Page,
		AccessPage,
		AddContextContainer,
		AdvancedSearchReviewerContainer,
		AdminPage,
		DecisionPage,
		DoiPage,
		ImportExportPage,
		ManageEmailsPage,
		ManageCatalogPage,
		SettingsPage,
		StartSubmissionPage,
		StatsEditorialPage,
		StatsPublicationsPage,
		StatsContextPage,
		StatsUsersPage,
		SubmissionWizardPage,
	},
});
