<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeTrackController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PostSaleController;
use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\MobileDashboardController;
use App\Http\Controllers\Mobile\MobileLeadController;
use App\Http\Controllers\Mobile\MobileTaskController;
use App\Http\Controllers\Mobile\MobileNotificationController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\textcontroller;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\MISController;
use App\Http\Controllers\Mobile\MobileMisController;
use App\Http\Controllers\SoftwareFeatureController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\WebExhibitionController;
use App\Http\Controllers\ShareAppController;
use App\Http\Controllers\UnifiedMessagingController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AgentLinkController;
use App\Http\Controllers\ZoneController;

Route::get('/', [AuthenticateController::class, 'show_login']);
Route::post('/login', [AuthenticateController::class, 'login'])->name('login');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('logout');
Route::post('/forgot-password', [AuthenticateController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthenticateController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthenticateController::class, 'reset'])->name('password.update');

Route::get('/lead/shared/{token}', [LeadController::class, 'showSharedLeadForm'])->name('lead.shared-form');
Route::post('/lead/shared/{token}', [LeadController::class, 'submitSharedLeadForm'])->name('lead.submit-shared-form');
Route::post('/submit-inquiry', [LeadController::class, 'submitInquiry'])->name('submit.inquiry');
Route::get('/lead/get-categories/{type}', [LeadController::class, 'getCategories']);
Route::get('/lead/get-subcategories/{category_id}', [LeadController::class, 'getSubCategories']);
Route::get('/lead/get-cities/{state}', [LeadController::class, 'getCities']);
Route::get('projects/{token}', [ProjectController::class, 'showPublic'])->name('project.public.show');

Route::prefix('mobile')->group(function () 
{
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('mobile.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('mobile.login');
    Route::middleware(['check.mobile.login'])->group(function () 
    {
        Route::get('/logout', [AuthController::class, 'logout'])->name('mobile.logout');
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('mobile.dashboard');
        Route::get('/dashboard-charts', [MobileDashboardController::class, 'getChartData'])->name('mobile.dashboard-charts');
        Route::post('/attendance/mark', [MobileDashboardController::class, 'markAttendance'])->name('mobile.attendance.mark');

        Route::get('/new-leads', [MobileDashboardController::class, 'newLeads'])->name('mobile.new-leads');
        Route::get('/transfer', [MobileDashboardController::class, 'transfer'])->name('mobile.transfer');
        Route::get('/allocated-leads', [MobileDashboardController::class, 'allocatedLeads'])->name('mobile.allocated-leads');
        Route::get('/pending-leads', [MobileDashboardController::class, 'pendingLeads'])->name('mobile.pending-leads');
        Route::get('/processing-leads', [MobileDashboardController::class, 'processingLeads'])->name('mobile.processing-leads');
        Route::get('/interested-leads', [MobileDashboardController::class, 'interestedLeads'])->name('mobile.interested-leads');
        Route::get('/not-picked-leads', [MobileDashboardController::class, 'notpickedLeads'])->name('mobile.not-picked-leads');
        Route::get('/visit-done-leads', [MobileDashboardController::class, 'visitDone'])->name('mobile.visit-done-leads');
        Route::get('/call-leads', [MobileDashboardController::class, 'callLeads'])->name('mobile.call-leads');
        Route::get('/visit-leads', [MobileDashboardController::class, 'visitLeads'])->name('mobile.visit-leads');
        Route::get('/cancelled-leads', [MobileDashboardController::class, 'cancelledLeads'])->name('mobile.cancelled-leads');
        Route::get('/not-interested-leads', [MobileDashboardController::class, 'not_interested_leads'])->name('mobile.not-interested-leads');
        Route::get('/not-reachable-leads', [MobileDashboardController::class, 'not_reachable_leads'])->name('mobile.not-reachable-leads');
        Route::get('/wrong-number-leads', [MobileDashboardController::class, 'wrong_number_leads'])->name('mobile.wrong-number-leads');
        Route::get('/lost-leads', [MobileDashboardController::class, 'lost_leads'])->name('mobile.lost-leads');
        Route::get('/future-leads', [MobileDashboardController::class, 'future_leads'])->name('mobile.future-leads');
        Route::get('/converted-leads', [MobileDashboardController::class, 'convertedLeads'])->name('mobile.converted-leads');
        Route::get('/channel-partner-leads', [MobileDashboardController::class, 'channelPartnerLeads'])->name('mobile.channel-partner-leads');
        Route::get('/booked', [MobileDashboardController::class, 'booked'])->name('mobile.booked');
        Route::get('/whatsapp', [MobileDashboardController::class, 'whatsapp'])->name('mobile.whatsapp');
        Route::get('/partially-complete', [MobileDashboardController::class, 'partially_complete'])->name('mobile.partially-complete');
        Route::get('/all-leads', [MobileDashboardController::class, 'all_leads'])->name('mobile.all-leads');
        Route::get('/completed', [MobileDashboardController::class, 'completed'])->name('mobile.completed');
        Route::get('/meeting-scheduled-leads', [MobileDashboardController::class, 'meetingScheduledLeads'])->name('mobile.meeting-scheduled-leads');

        Route::get('/get-users', [MobileDashboardController::class, 'getUsers'])->name('mobile.get-users');
        Route::post('/share-leads', [MobileDashboardController::class, 'shareLeads'])->name('mobile.share-leads');
        Route::post('/update-lead-status', [MobileLeadController::class, 'updateStatus'])->name('mobile.update-lead-status');
        Route::get('/leads/create', [MobileLeadController::class, 'addLeads'])->name('mobile.leads.create');
        Route::get('/lead-edit/{id}', [MobileLeadController::class, 'lead_edit'])->name('mobile.leads.edit');
        Route::put('/update-lead/{id}', [MobileLeadController::class, 'update_lead'])->name('mobile.update-lead');
        Route::post('/create-leads', [MobileLeadController::class, 'create_leads'])->name('mobile.create-leads');
        Route::post('/quick-leads', [MobileLeadController::class, 'quick_Lead'])->name('mobile.quick-leads');

        Route::get('/tasks', [MobileTaskController::class, 'index'])->name('mobile.tasks');
        Route::post('/tasks', [MobileTaskController::class, 'store'])->name('mobile.task.store');
        Route::post('/tasks/{id}', [MobileTaskController::class, 'update'])->name('mobile.task.update');
        Route::post('/tasks/{id}/complete', [MobileTaskController::class, 'complete'])->name('mobile.task.complete');
        Route::post('/tasks/allocate', [MobileTaskController::class, 'allocate'])->name('mobile.task.allocate');

        Route::get('/notification', [MobileNotificationController::class, 'notification'])->name('mobile.notifications');
        Route::get('/profile', [MobileDashboardController::class, 'profile'])->name('mobile.user.profile');
        Route::get('/view-comments/{lead_id}', [MobileDashboardController::class, 'view_comments'])->name('mobile.view-comments');
        Route::post('/profile/update', [MobileDashboardController::class, 'profile_update'])->name('mobile.profile.update');
        Route::get('/attendance/status', [MobileDashboardController::class, 'status'])->name('mobile.attendance.status');
    });
});

Route::middleware(['check.login', 'reception.only'])->group(function () 
{
    Route::prefix('setting')->controller(SettingsController::class)->group(function() 
    {
        Route::get('/profile', 'profile')->name('setting.profile');
        Route::post('/profile/update', 'updateProfile')->name('setting.update_profile');
        Route::post('/password/update', 'updatePassword')->name('setting.update_password');
        Route::get('/change/logo', 'change_logo')->name('setting.logo');
        Route::get('/login/log', 'login_log')->name('setting.login_log');
        Route::post('/update/logo', 'update_logo')->name('setting.update_logo');
        Route::get('/settings/ratings', [App\Http\Controllers\SettingsController::class, 'ratings'])
        ->name('settings.ratings');
    });

    Route::middleware(['check.software.type:universal_modules'])->group(function () 
    {
        Route::resource('users', StaffManagementController::class);
        Route::post('/users/import', [StaffManagementController::class, 'import'])->name('users.import');
        Route::post('/users/update-status', [StaffManagementController::class, 'updateStatus'])->name('users.update-status');
        Route::delete('/users/{id}/check-delete', [StaffManagementController::class, 'checkDelete'])->name('users.check_delete');
        Route::get('/promote/list', [StaffManagementController::class, 'promote_list'])->name('promote.list');
        Route::get('/promote/show/{id}', [StaffManagementController::class, 'showPromote'])->name('promote.show');
        Route::post('/promote/approved/{id}', [StaffManagementController::class, 'approved'])->name('promote.approved');
        Route::get('/designation/list', [StaffManagementController::class, 'designation_list'])->name('designation.list');
        Route::post('/designation/update/{id}', [StaffManagementController::class, 'update_designation'])->name('designation.update');
        Route::post('/designation/store', [StaffManagementController::class, 'store_designation'])->name('designation.store');
        Route::get('/company/hierarchy', [StaffManagementController::class, 'company_hierarchy'])->name('company.hierarchy');

        Route::get('/category/list', [StaffManagementController::class, 'category_list'])->name('category.list');
        Route::put('/category/update/{id}', [StaffManagementController::class, 'update_category'])->name('update.category');
        Route::post('/category/store', [StaffManagementController::class, 'store_category'])->name('category.store');
    });

    Route::middleware(['check.software.type:all_modules,real_state_only'])->group(function () 
    {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/search', [DashboardController::class, 'search'])->name('search');
        Route::post('/attendance-toggle', [DashboardController::class, 'toggle'])->name('attendance.toggle');
        
        Route::get('/dashboard/get-chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::get('/dashboard/export-chart', [DashboardController::class, 'exportChartData'])->name('dashboard.export-chart');
        Route::post('/dashboard/analytics', [DashboardController::class, 'getAnalyticsData'])->name('dashboard.analytics');
        Route::post('/dashboard/export-analytics', [DashboardController::class, 'exportAnalyticsData'])->name('dashboard.export.analytics');
        
        Route::get('/role-permission/{secret}', [RolePermissionController::class, 'form'])->name('role.permission.form');
        Route::post('/role-permission/store', [RolePermissionController::class, 'store'])->name('role.permission.store');
        Route::post('/role-permission/update/{id}', [RolePermissionController::class, 'update'])->name('role.permission.update');
        Route::post('/role-permission/delete/{id}', [RolePermissionController::class, 'delete'])->name('role.permission.delete');
        Route::post('/permission/store', [RolePermissionController::class, 'storePermission'])->name('permission.store');
        Route::post('/permission/update/{id}', [RolePermissionController::class, 'updatePermission'])->name('permission.update');
        Route::post('/permission/delete/{id}', [RolePermissionController::class, 'deletePermission'])->name('permission.delete');

        Route::get('/form/field', [MasterController::class, 'form_field'])->name('form.field');
        Route::post('/form/field', [MasterController::class, 'update_settings'])->name('settings.update');
        Route::get('/project', [MasterController::class, 'project_name'])->name('project.name');
        Route::post('/project/store', [MasterController::class, 'store_project'])->name('project.store');
        Route::put('/project/update/{id}', [MasterController::class, 'update_project'])->name('project.update');

        Route::get('/campaign', [MasterController::class, 'campaign'])->name('campaign');
        Route::put('/campaign/update/{id}', [MasterController::class, 'campaign_update'])->name('campaign.update');
        Route::post('/campaign/store', [MasterController::class, 'campaign_store'])->name('campaigns.store');

        Route::get('/source/platform', [MasterController::class, 'source_platform'])->name('source.platform');
        Route::put('/source/update/{id}', [MasterController::class, 'source_update'])->name('source.update');
        Route::post('/source/store', [MasterController::class, 'source_create'])->name('source.store');

        Route::get('/check/list', [MasterController::class, 'check_list'])->name('check.list');
        Route::put('/checklist/update/{id}', [MasterController::class, 'checklist_update'])->name('checklist.update');
        Route::post('checklist/store', [MasterController::class, 'checklist_store'])->name('checklist.store');

        Route::get('/project/category', [MasterController::class, 'project_category'])->name('project.category');
        Route::put('/project-category/update/{id}', [MasterController::class, 'category_update'])->name('category.update');
        Route::post('/project-category/store', [MasterController::class, 'project_category_store'])->name('project_category.store');

        Route::get('/project/sub-category', [MasterController::class, 'project_sub_category'])->name('project.sub_category');
        Route::post('/sub-category', [MasterController::class, 'sub_category_store'])->name('sub_category.store');
        Route::put('/sub-category/{id}', [MasterController::class, 'sub_category_update'])->name('sub_category.update');
        
        Route::get('/attendance', [MasterController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [MasterController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{id}', [MasterController::class, 'update'])->name('attendance.update');
        
        Route::get('inquiry-question', [MasterController::class, 'inquiry_question'])->name('inquiry_question');
        Route::post('inquiry-question/store', [MasterController::class, 'inquiry_question_store'])->name('inquiry-question.store');
        Route::post('inquiry-question/update', [MasterController::class, 'inquiry_question_update'])->name('inquiry-question.update');

        Route::get('/integration-settings', [MasterController::class, 'integration_settings'])->name('integration.settings');
        Route::post('/integration-settings', [MasterController::class, 'integration_store'])->name('integration.store');
        Route::put('/integration-settings/{id}', [MasterController::class, 'integration_update'])->name('integration.update');
        Route::delete('/integration-settings/{id}', [MasterController::class, 'integration_destroy'])->name('integration.destroy');
        
        Route::get('/integrations/facebook/status', [IntegrationController::class, 'checkFacebookSyncStatus'])->name('integrations.facebook.status');
        Route::get('/get-project-name/{id}', [LeadController::class, 'getProjectName']);
        Route::post('/integration/auto-sync/{integrationType}', [IntegrationController::class, 'updateAutoSync'])->name('integration.auto-sync.update');

        Route::prefix('lead')->controller(LeadController::class)->group(function () 
        {
            Route::get('/add', 'add_lead')->name('lead.add');
            Route::post('/import/upload','importUpload')->name('lead.import.upload');
            Route::get('/all-lead', 'all_lead')->name('lead.all_lead');
            Route::post('/generate-share-link', 'generateShareLink')->name('lead.generate-share-link');
            Route::post('/update-status','updateStatus')->name('lead.updateStatus');
            Route::post('/create', 'create_lead')->name('lead.index');
            Route::get('/import','showImportForm')->name('lead.import');
            Route::post('/import', 'processImport')->name('lead.import.process');
            Route::get('/allocate', 'allocate_lead')->name('lead.allocate');
            Route::post('allocate/lead', 'allocateLeads')->name('lead.allocate_user');
            Route::get('/unallocated', 'unallocated_lead')->name('lead.unallocated');
            Route::get('/new', 'new_lead')->name('lead.new');
            Route::get('/transfer-lead', 'transfer_lead')->name('lead.transfer_lead');
            Route::get('/transfer-history', 'transfer_history')->name('lead.transfer_history');
            Route::get('/pending', 'pending')->name('lead.pending');
            Route::get('/processing', 'processing')->name('lead.processing');
            Route::get('/interested', 'interested')->name('lead.interested');
            Route::get('/call-scheduled', 'call_scheduled')->name('lead.call_scheduled');
            Route::get('/meeting-scheduled', 'meeting_scheduled')->name('lead.meeting_scheduled');
            Route::get('/whatsapp', 'whatsapp')->name('lead.whatsapp');
            Route::get('/visit-scheduled', 'visit_scheduled')->name('lead.visit_scheduled');
            Route::get('/visit-done' , 'visit_done')->name('lead.visit_done');
            Route::get('/booked', 'booked')->name('lead.booked');
            Route::get('/completed', 'completed')->name('lead.completed');
            Route::get('/cancelled', 'cancelled')->name('lead.cancelled');
            Route::get('/future', 'future')->name('lead.future');
            Route::get('/transfer', 'transfer')->name('lead.transfer');
            Route::get('/transfer-list', 'transfer_leads')->name('transfer_list.lead');
            Route::post('/lead/transfer-user', 'transfer_user')->name('lead.transfer_user');
            Route::get('/channel-partner', 'channel_partner')->name('lead.channel_partner');
            Route::get('/not-interested', 'not_interested')->name('lead.not_interested');
            Route::get('/not-picked', 'not_picked')->name('lead.not_picked');
            Route::get('/lost', 'lost')->name('lead.lost');
            Route::get('/wrong-number', 'wrong_number')->name('lead.wrong_number');
            Route::get('/not-reachable', 'not_reachable')->name('lead.not_reachable');
            Route::get('/edit/{id}', 'edit_lead')->name('lead.edit');
            Route::post('/update/{id}', 'update_lead')->name('lead.update');
            Route::get('/{lead}/comments', 'getComments')->name('leads.comments');
            Route::post('/quick-lead', 'quick_lead')->name('lead.quick_add');
            Route::post('/duplicate', 'duplicateLead')->name('lead.duplicate');
            Route::post('/share', 'shareLead')->name('lead.share');
            Route::post('/add-projects', 'addProjects')->name('lead.add-projects');
            Route::post('/get-project-names','getProjectNames')->name('lead.get-project-names');
            Route::post('/get-lead-projects', 'getLeadProjects')->name('lead.get-lead-projects');
            Route::get('/{id}/project-visits', 'getLeadProjectVisits')->name('lead.project-visits');
            Route::get('/filter-lead', 'filterLeads')->name('lead.filter');
            Route::delete('/delete', 'delete')->name('lead.delete');
        });

        Route::get('/leads/filter-lead', [LeadController::class, 'filterLeads'])->name('leads.filter.leads');

        Route::prefix('task')->controller(TaskController::class)->group(function() 
        {
            Route::get('/create/{id?}', 'create')->name('task.create');
            Route::post('/store/{id?}', 'store')->name('task.store');
            Route::get('/list', 'list')->name('task.list');
            Route::delete('/delete/{id}', 'destroy')->name('task.destroy');
            Route::post('/update-status/{id}', 'updateStatus')->name('task.update.status');
            Route::post('/task-project-store', 'task_project_store')->name('task.project.store');
            Route::post('/project/update/{id}', 'task_project_update')->name('task.project.update');
            Route::delete('/project/delete/{id}','task_project_destroy')->name('task.project.delete');
            Route::post('/project/update-status/{id}', 'updateProjectStatus')->name('task.project.update-status');
        });

        Route::resource('project-details', ProjectController::class);
        Route::get('project-details/get-categories/{type}', [ProjectController::class, 'getCategories']);
        Route::get('project-details/get-subcategories/{categoryId}', [ProjectController::class, 'getSubcategories']);
        Route::post('project-details/remove-image', [ProjectController::class, 'removeImage'])->name('project-details.remove_image');

        Route::prefix('event')->controller(EventController::class)->group(function() 
        {
            Route::get('/', 'index')->name('event.index');
            Route::get('/comments/{id}', 'showComments')->name('event.comments');
        });

        Route::prefix('attendance')->controller(AttendanceController:: class)->group(function()
        {
            Route::get('/daily', 'daily')->name('attendance.daily');
            Route::get('/monthly', 'monthly')->name('attendance.monthly');
        });

        Route::prefix('employee')->controller(EmployeeTrackController:: class)->group(function()
        {
            Route::get('/tracking', 'tracking')->name('employee.tracking');
            Route::get('timeline', 'timeline')->name('employee.timeline');
        });

        Route::get('/expense', [ExpenseController::class, 'index'])->name('expense.index');
        Route::post('/expense', [ExpenseController::class, 'store'])->name('expense.store');
        Route::post('/expense/bulk-accept', [ExpenseController::class, 'bulkAccept'])->name('expense.bulk-accept');
        Route::post('/expense/bulk-reject', [ExpenseController::class, 'bulkReject'])->name('expense.bulk-reject');
        Route::post('/expense/{id}/accept', [ExpenseController::class, 'accept'])->name('expense.accept');
        Route::post('/expense/{id}/reject', [ExpenseController::class, 'reject'])->name('expense.reject');
        Route::post('/expense/{id}/clear', [ExpenseController::class, 'clear'])->name('expense.clear');
        Route::get('/expense/{id}/images', [ExpenseController::class, 'getImages'])->name('expense.images');

        Route::prefix('report')->controller(ReportController:: class)->group(function()
        {
            Route::get('/reports', 'reports')->name('reports');
            Route::get('/dayend-reports', 'dayend_reports')->name('report.dayend_reports');
            Route::get('/talecaller-reports', 'talecaller_reports')->name('report.talecaller_reports');
            Route::get('/salesman-reports', 'salesman_reports')->name('report.salesman_reports');
            Route::get('/campaign-reports', 'campaign_reports')->name('report.campaign_reports');
            Route::get('/source-reports', 'source_reports')->name('report.source_reports');
            Route::get('/classification-reports', 'classificationReports')->name('report.classification_reports');
            Route::get('/project-reports', 'project_reports')->name('report.project_reports');
            Route::get('/category-reports', 'category_reports')->name('report.category_reports');
            Route::get('/sub-category-reports', 'sub_category_reports')->name('report.sub_category_reports');
            Route::get('/city-reports', 'city_reports')->name('report.city_reports');
            Route::get('/state-reports', 'state_reports')->name('report.state_reports');
            Route::get('/address-reports', 'address_reports')->name('report.address_reports');
            Route::get('/interested-reports', 'interested_reports')->name('report.interested_reports');
            Route::get('/visit-reports', 'visit_reports')->name('report.visit_reports');
            Route::get('/call-reports', 'call_reports')->name('report.call_reports');
            Route::get('/call-details', 'call_details')->name('report.call_details');

            Route::any('/smart-lead', [ReportController::class, 'smart_lead'])->name('report.smart_lead');
            Route::post('/get-categories', [ReportController::class, 'getCategories'])->name('get-categories');
            Route::post('/get-subcategories', [ReportController::class, 'getSubCategories'])->name('get-subcategories');
            Route::post('/get-cities', [ReportController::class, 'getCities'])->name('get-cities');
            Route::post('/log-call', [ReportController::class, 'logCall'])->name('log-call');
            Route::get('/initiate-call/{lead}', [ReportController::class, 'initiateCall'])->name('initiate-call');
            Route::get('/task-report-summary', [ReportController::class, 'taskReportSummary'])->name('task-report-summary');
            Route::get('/task-overdue-summary', [ReportController::class, 'overdueTasksReport'])->name('task-overdue-summary');
            Route::get('/upcoming-tasks', [ReportController::class, 'upcomingTasksReport'])->name('upcoming-tasks');
            Route::get('/task-completion', [ReportController::class, 'taskCompletionReport'])->name('task-completion');
            Route::get('/project-wise-task', [ReportController::class, 'projectWiseTaskReport'])->name('project-wise-task');
            Route::get('/communication-reports', 'communication_reports')->name('report.communication_reports');
            Route::get('/agent-call-details/{id?}', 'agentCallDetails')
            ->name('report.agent_call_details');
            Route::get('/client-communications', [ReportController::class, 'clientCommunications'])
            ->name('report.client_communications');
        });

        Route::post('/leads/toggle-pin', [LeadController::class, 'togglePin'])->name('lead.toggle-pin');
        Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations.index');
        Route::post('/integrations/housing/sync', [IntegrationController::class, 'syncHousing'])->name('integrations.housing.sync');
        Route::post('/integrations/facebook/sync', [IntegrationController::class, 'syncFacebook'])->name('integrations.facebook.sync');
        Route::post('/facebook/token/exchange', [IntegrationController::class, 'exchangeToken'])->name('facebook.token.exchange');
        Route::post('/facebook/pages/fetch', [IntegrationController::class, 'fetchPages'])->name('facebook.pages.fetch');
        Route::post('/facebook/groups/fetch', [IntegrationController::class, 'groupPages'])->name('facebook.group.fetch');
        Route::post('/facebook/campaigns/fetch', [IntegrationController::class, 'fetchCampaigns'])->name('facebook.campaigns.fetch');

        Route::prefix('notifications')->name('notifications.')->group(function () 
        {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/mark-all-read', [SettingsController::class, 'markAllAsRead'])->name('markAllRead');
        });

        Route::prefix('setting')->controller(SettingsController::class)->group(function() 
        {
            Route::get('/integration', 'integration')->name('setting.integration');
            Route::get('/notification', 'notification')->name('setting.notification');
            Route::post('/notification/mark-read', 'markAllRead')->name('setting.notification.mark_all_read');
        });

        Route::get('/mis/targets', [MISController::class, 'targets'])->name('mis.targets');
        Route::post('/mis/targets/save', [MISController::class, 'saveTargets'])->name('mis.targets.save');  
        Route::post('/mis/admin/update', [MISController::class, 'adminUpdate'])->name('mis.admin.update');
        Route::get('/mis/summary-report', [MISController::class, 'summaryReport'])->name('mis.summary-report');
        Route::get('/mis/daily-report', [MISController::class, 'dailyReport'])->name('mis.daily-report');
        Route::get('/mis/get-week-daily-data', [MISController::class, 'getWeekDailyData'])->name('mis.get.week.daily.data');
        Route::post('/mis/admin/update-daily-achieved', [MISController::class, 'updateDailyAchieved'])->name('mis.admin.update.daily.achieved');
        Route::get('/mis/get-autoassign-status', [MISController::class, 'getAutoAssignStatus'])->name('mis.get.autoassign.status');
        
        Route::get('/mis-points', [MasterController::class, 'mis_points'])->name('mis.points');
        Route::post('/mis-points/store', [MasterController::class, 'mis_points_store'])->name('mis.points.store');
        Route::put('/mis-points/update/{id}', [MasterController::class, 'mis_points_update'])->name('mis.points.update');
        Route::delete('/mis-points/destroy/{id}', [MasterController::class, 'mis_points_destroy'])->name('mis.points.destroy');

        Route::get('/premium-features', [SoftwareFeatureController::class, 'index'])->name('premium.features');
        Route::post('/premium-features/request', [SoftwareFeatureController::class, 'requestAccess'])->name('premium-features.request');
        Route::post('/premium-features/activate', [SoftwareFeatureController::class, 'activateFeature'])->name('premium-features.activate');
        Route::match(['get', 'post'], '/start-free-trial', [SoftwareFeatureController::class, 'startFreeTrial'])->name('start-free-trial');
        Route::post('/advertisement/deactivate', [AdvertisementController::class, 'deactivate'])->name('advertisement.deactivate');

        Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('support.index');
        Route::post('/support-tickets', [SupportTicketController::class, 'store'])->name('support.store');
        Route::put('/support-tickets/{id}', [SupportTicketController::class, 'update'])->name('support.update');
        Route::patch('/support-tickets/{id}/toggle', [SupportTicketController::class, 'toggleStatus'])->name('support.toggle');

        Route::prefix('post-sale')->name('post-sale.')->group(function () 
        {
            Route::get('/', [PostSaleController::class, 'index'])->name('index');
            Route::post('/', [PostSaleController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PostSaleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PostSaleController::class, 'update'])->name('update');
            Route::delete('/{id}', [PostSaleController::class, 'destroy'])->name('destroy');

            Route::get('/subcategories/{category}', [PostSaleController::class, 'getSubcategories']);

            Route::get('/{id}/documents', [PostSaleController::class, 'getDocuments']);
            Route::post('/{id}/upload-document', [PostSaleController::class, 'uploadDocument']);
            Route::delete('/document/{id}', [PostSaleController::class, 'deleteDocument']);

            Route::post('/rate-link', [PostSaleController::class, 'generateRatingLink'])->name('rate-link');
        });
    });

    Route::middleware(['check.software.type:real_state_only'])->group(function () 
    {
        Route::prefix('inventory')->group(function () 
        {
            Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
            Route::post('/store', [InventoryController::class, 'store'])->name('inventory.store');
            Route::post('/update-sale', [InventoryController::class, 'updateSale'])->name('inventory.updateSale');
            Route::post('/sale-history', [InventoryController::class, 'getSaleHistory'])->name('inventory.saleHistory');
            Route::post('/import', [InventoryController::class, 'import'])->name('inventory.import');
            Route::get('/download-template', [InventoryController::class, 'downloadTemplate'])->name('inventory.downloadTemplate');
        });
    });

    Route::middleware(['check.software.type:task_management'])->group(function () 
    {
        Route::prefix('task')->controller(TaskController::class)->group(function() 
        {
            Route::get('/create/{id?}', 'create')->name('task.create');
            Route::post('/store/{id?}', 'store')->name('task.store');
            Route::get('/list', 'list')->name('task.list');
            Route::delete('/delete/{id}', 'destroy')->name('task.destroy');
            Route::post('/update-status/{id}', 'updateStatus')->name('task.update.status');
            Route::post('/task-project-store', 'task_project_store')->name('task.project.store');
            Route::post('/project/update/{id}', 'task_project_update')->name('task.project.update');
            Route::delete('/project/delete/{id}','task_project_destroy')->name('task.project.delete');
            Route::post('/project/update-status/{id}', 'updateProjectStatus')->name('task.project.update-status');
        });
    });

    Route::middleware(['check.software.type:mis_management'])->group(function () 
    {
        Route::get('/mis/targets', [MISController::class, 'targets'])->name('mis.targets');
        Route::post('/mis/targets/save', [MISController::class, 'saveTargets'])->name('mis.targets.save');  
        Route::post('/mis/admin/update', [MISController::class, 'adminUpdate'])->name('mis.admin.update');
        Route::get('/mis/summary-report', [MISController::class, 'summaryReport'])->name('mis.summary-report');
        Route::get('/mis/daily-report', [MISController::class, 'dailyReport'])->name('mis.daily-report');
        Route::get('/mis/get-week-daily-data', [MISController::class, 'getWeekDailyData'])->name('mis.get.week.daily.data');
        Route::post('/mis/admin/update-daily-achieved', [MISController::class, 'updateDailyAchieved'])->name('mis.admin.update.daily.achieved');
        Route::get('/mis/get-autoassign-status', [MISController::class, 'getAutoAssignStatus'])->name('mis.get.autoassign.status');
        Route::get('/mis-points', [MasterController::class, 'mis_points'])->name('mis.points');
        Route::post('/mis-points/store', [MasterController::class, 'mis_points_store'])->name('mis.points.store');
        Route::put('/mis-points/update/{id}', [MasterController::class, 'mis_points_update'])->name('mis.points.update');
        Route::delete('/mis-points/destroy/{id}', [MasterController::class, 'mis_points_destroy'])->name('mis.points.destroy');
        Route::get('/mis/get-team-points', [MISController::class, 'getTeamPoints'])->name('mis.get.team.points');
    
        Route::post('/mis/save-daily-entries', [MISController::class, 'saveDailyEntries'])->name('mis.save.daily.entries');
        Route::get('/mis/get-daily-data', [MISController::class, 'getDailyData'])->name('mis.get.daily.data');
        Route::get('/mis/get-week-number', [MISController::class, 'getWeekNumber'])->name('mis.get.week.number');
    });

    Route::prefix('exhibitions')->name('exhibition.')->group(function () 
    {
        Route::get('/', [WebExhibitionController::class, 'index'])->name('index');
        Route::post('/', [WebExhibitionController::class, 'store'])->name('store');
        Route::put('/{id}', [WebExhibitionController::class, 'update'])->name('update');
        Route::delete('/{id}', [WebExhibitionController::class, 'destroy'])->name('destroy');
        
        Route::get('/{id}/view', [WebExhibitionController::class, 'view'])->name('view');
        Route::get('/{id}/leads-page', [WebExhibitionController::class, 'leadsPage'])->name('leads.page');
        Route::put('/leads/{id}', [WebExhibitionController::class, 'updateLead'])->name('leads.update');
        Route::get('/{id}/leads', [WebExhibitionController::class, 'getLeads'])->name('leads');
        Route::get('/leads/{id}', [WebExhibitionController::class, 'getLeadDetails'])->name('lead.details');
        Route::post('/{id}/activate', [WebExhibitionController::class, 'activate'])->name('activate');
        Route::post('/{id}/store', [WebExhibitionController::class, 'storeLead'])->name('leads.store');
        Route::delete('/{id}/{exhibition_id}', [WebExhibitionController::class, 'destroyLead']);
        Route::post('/{lead}/convert', [WebExhibitionController::class, 'convertLeadToCRM'])->name('leads.convert');
        Route::post('/convert-multiple', [WebExhibitionController::class, 'convertMultipleLeads'])->name('leads.convert.multiple');       
        Route::post('{exhibition}/share/create', [WebExhibitionController::class, 'createShareLink'])->name('share.create');
        Route::get('{exhibition}/share/links', [WebExhibitionController::class, 'getShareLinks'])->name('share.links');
        Route::post('/{exhibition}/leads/import', [WebExhibitionController::class, 'import'])->name('exhibition.leads.import');
        Route::post('/{exhibition}/leads/import', [WebExhibitionController::class, 'import'])->name('leads.import');
    });
    
    Route::prefix('messaging')->group(function () 
    {
        Route::get('/', [UnifiedMessagingController::class, 'index'])->name('messaging.index');
        Route::post('/send', [UnifiedMessagingController::class, 'sendMessage'])->name('messaging.send');
        Route::post('/send-with-attachments', [UnifiedMessagingController::class, 'sendWithAttachments'])->name('messaging.send.with-attachments');
        Route::get('/templates/{channel}', [UnifiedMessagingController::class, 'getTemplates']);
        Route::get('/templates/{channel}/{templateId}/preview', [UnifiedMessagingController::class, 'getTemplatePreview']);
        Route::get('/channels', [UnifiedMessagingController::class, 'getChannels']);
        Route::get('/leads', [UnifiedMessagingController::class, 'getLeads']);
        Route::get('/exhibition/leads/details', [UnifiedMessagingController::class, 'getExhibitionLeadsDetails']);
        Route::get('/create-template', [UnifiedMessagingController::class, 'createTemplatePage'])->name('messaging.templates.create');
        Route::post('/store-template', [UnifiedMessagingController::class, 'storeTemplate'])->name('messaging.templates.store');
        Route::post('/cleanup-temp-files', [UnifiedMessagingController::class, 'cleanupTempFiles'])->name('messaging.cleanup-temp-files');
        Route::get('/{template}/edit', [UnifiedMessagingController::class, 'edit'])->name('messaging.templates.edit');
        Route::put('/{template}', [UnifiedMessagingController::class, 'update'])->name('messaging.templates.update');
        Route::delete('/{template}', [UnifiedMessagingController::class, 'destroy'])->name('messaging.templates.destroy');
    });

    Route::post('/exhibitions/{id}/toggle-auto-welcome', [WebExhibitionController::class, 'toggleAutoWelcome'])->name('exhibitions.toggle-auto-welcome');
    Route::get('/exhibitions/{id}/get-details', [WebExhibitionController::class, 'getExhibitionDetails'])->name('exhibitions.get-details');
    Route::get('/exhibition/{id}/message', [UnifiedMessagingController::class, 'exhibitionMessage'])->name('exhibition.message');
    Route::get('/exhibition/share/{shareCode}', [WebExhibitionController::class, 'accessShareLink'])->name('exhibition.share.access');
    Route::post('/exhibition-share/{shareCode}/submit', [WebExhibitionController::class, 'submitShareForm'])->name('exhibition.share.submit');
    Route::post('/lead/assign-projects', [LeadController::class, 'assignProjects'])->name('lead.assign-projects');
    Route::get('/lead/{id}/matching-properties', [LeadController::class, 'getMatchingProperties'])->name('lead.matching-properties');
    Route::post('/lead/{id}/refresh-matching', [LeadController::class, 'refreshMatchingProperties'])->name('lead.refresh-matching');
    Route::post('/property/share-whatsapp', [LeadController::class, 'sharePropertyOnWhatsApp'])->name('property.share-whatsapp');
    Route::get('/property/{id}/details', [LeadController::class, 'getPropertyDetails'])->name('property.details');
    Route::get('/lead/{id}/details', [LeadController::class, 'getDetails'])->name('lead.details');
    Route::get('/properties', [MasterController::class, 'property_name'])->name('property.name');
    Route::post('/properties/store', [MasterController::class, 'store_property'])->name('property.store');
    Route::put('/properties/{id}', [MasterController::class, 'update_property'])->name('property.update');

    Route::prefix('agent-links')->group(function () {
        Route::get('/create', [AgentLinkController::class, 'create'])->name('agent-links.create');
        Route::post('/', [AgentLinkController::class, 'store'])->name('agent-links.store');
        Route::get('/{id}', [AgentLinkController::class, 'show'])->name('agent-links.show');
        Route::delete('/{id}', [AgentLinkController::class, 'destroy'])->name('agent-links.destroy');
        Route::get('/{id}/download-barcode', [AgentLinkController::class, 'downloadBarcode'])->name('agent-links.download-barcode');
    });
});
Route::get('/agent/{identifier}', [AgentLinkController::class, 'publicForm'])->name('agent.public-form');
Route::post('/agent/{identifier}/submit', [AgentLinkController::class, 'submitLead'])->name('agent.submit-lead');
Route::get('/agent/{identifier}/thank-you', [AgentLinkController::class, 'thankYou'])->name('agent.thank-you');
Route::prefix('zone')->name('zone.')->group(function() {
    Route::get('/', [ZoneController::class, 'index'])->name('index');
    Route::get('/create', [ZoneController::class, 'create'])->name('create');
    Route::post('/', [ZoneController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ZoneController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ZoneController::class, 'update'])->name('update');
    Route::delete('/{id}', [ZoneController::class, 'destroy'])->name('destroy');
    Route::post('/import', [ZoneController::class, 'import'])->name('import');
    Route::post('/{id}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/by-city/{cityId}', [ZoneController::class, 'getByCity'])->name('by-city');
    Route::get('/rajasthan/cities', [ZoneController::class, 'getCities'])->name('rajasthan.cities');
});
Route::get('/create-storage-link', function () 
{
    Artisan::call('storage:link');
    return 'Storage link created!';
});

Route::get('/clear-all', function () 
{
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');
    return 'All caches cleared and optimized!';
});

Route::get('/refresh-storage', function () 
{
    $src = storage_path('app/public');
    $dst = public_path('storage');
    \File::deleteDirectory($dst);
    \File::copyDirectory($src, $dst);
    return 'Storage refreshed!';
});