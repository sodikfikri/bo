<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'dashboard/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;
$route['register'] = 'auth/register';
$route['register-intrax/(:any)'] = 'auth/register_intrax';
$route['login-intrax'] = 'auth/login_intrax';
$route['logout-intrax'] = "auth/login_intrax/logout";
$route['register-auth'] = 'auth/register/save_register';
$route['register-auth-intrax'] = 'auth/register_intrax/save_register';
$route['register-otp-input/(:any)'] = 'auth/register/inputOTP/$1';
$route['register-otp-input-intrax/(:any)'] = 'auth/register_intrax/inputOTP/$1';
$route['resendOTP'] = 'auth/register/resendOTP';
$route['submitOTPRegister'] = 'auth/register/submitOTP';
$route['checkEmailExist'] = 'auth/register/checkEmailExist';
$route['login'] = "auth/login";
$route['logout'] = "auth/login/logout";
$route['forgot-password'] = "auth/forgot_password";
$route['forgot-password-otpauth/(:any)'] = "auth/forgot_password/OTPAuth/$1";
$route['ForgotPasswordResendOTP'] = 'auth/forgot_password/resendOTP';
$route['submitOTPForgotPassword'] = 'auth/forgot_password/submitOTP';
$route['change-password/(:any)/(:any)'] = 'auth/forgot_password/change_password/$1/$2';

$route['addons'] = 'license/addons';
$route['addonsTakeTrial'] = 'license/addons/takeTrialAddons';
$route['addonsPrepareDataForBuy'] = 'license/addons/PrepareDataForBuy';
$route['addonsPrepareDataForCheckOut'] = 'master/prospective_employees/PrepareDataForCheckOut';
$route['approveAllBranch'] = 'master/request_register/approveAllBranch';
$route['setting-device/(:any)'] = 'license/setting_addons/index/$1';

$route['company-setting'] = 'setting';
$route['send-link-registration'] = 'setting/linkRegister';
$route['registration-access'] = 'registration_access';
$route['request-register'] = 'master/request_register';
$route['regionGetProvince'] = 'setting/getProvince';
$route['regionGetCity'] = 'setting/getCity';
$route['companySettingUpdate'] = 'setting/updateSetting';
$route['master-user'] = 'master/user';
$route['save-user']   = 'master/user/save_user';

$route['schedule-work-hours'] = 'schedule/work_hours';
$route['schedule-work-hours-submit'] = 'schedule/work_hours/savedata';
$route['schedule-work-hours-detail'] = 'schedule/work_hours/getDetailData';
$route['schedule-work-hours-delete/(:any)'] = 'schedule/work_hours/delData/$1';

$route['schedule-holidays'] = 'schedule/holidays';
$route['schedule-holidays/submit'] = 'schedule/holidays/savedata';
$route['schedule-holidays/delete'] = 'schedule/holidays/deldata';

$route['check-email-exists'] = 'master/user/checkEmail';
$route['check-phone-exists'] = 'master/user/checkPhone';

$route['add-user']     = 'master/user/add_user';
$route['edit-user/(:any)'] = 'master/user/add_user/$1';
$route['delete-user/(:any)'] = 'master/user/deleteUser/$1';
$route['user-profile'] = 'setting_user';
$route['save-profile-changes'] = 'setting_user/saveChanges';
$route['master-area'] = 'master/area';
$route['master-apa-aja'] = 'master/apa_aja';
$route['save-area']   = 'master/area/saveArea';
$route['delete-area/(:any)'] = 'master/area/deleteArea/$1';
$route['check-area-code-exists'] = 'master/area/checkCodeExists';
$route['check-area-name-exists'] = 'master/area/checkNameExists';
$route['master-institution'] = 'master/institution';
$route['save-institution']   = 'master/institution/saveInstitution';
$route['save-institution-temp']   = 'master/institution/savedata';
$route['save-request-register']   = 'master/request_register/saveInstitution';
$route['delete-institution/(:any)'] = 'master/institution/deleteInstitution/$1';
$route['check-institution-code-exists'] = 'master/institution/checkCodeExists';
$route['check-institution-name-exists'] = 'master/institution/checkNameExists';

$route['master-branch'] = 'master/cabang';
$route['save-branch']   = 'master/cabang/saveCabang';
$route['filter-branch']   = 'master/cabang/filterBranch';
$route['show-employee']   = 'master/cabang/showEmployee';
$route['show-employee-request-register']   = 'master/request_register/showEmployee';
$route['save-branch-method']   = 'master/cabang/saveCabangMethod';
$route['delete-branch/(:any)'] = 'master/cabang/deleteCabang/$1';
$route['check-cabang-code-exists'] = 'master/cabang/checkCodeExists';
$route['check-cabang-name-exists'] = 'master/cabang/checkNameExists';

$route['master-leave-categories'] = 'master/leave_categories';
$route['master-leave-categories/submit'] = 'master/leave_categories/save_act';
$route['master-leave-categories/delete/(:any)'] = 'master/leave_categories/del_act/$1';

$route['master-ijin'] = 'master/ijin';
$route['save-ijin']   = 'master/ijin/saveIjin';
$route['delete-ijin/(:any)'] = 'master/ijin/deleteIjin/$1';

$route['master-device'] = 'master/device';
$route['save-device']   = 'master/device/saveDevice';
$route['device-check-sn-exists'] = 'master/device/checkSNExist';
$route['load-cabang']   = 'master/cabang/getCabangByArea';
$route['delete-device/(:any)'] = 'master/device/deleteDevice/$1';
$route['load-table-device'] = 'master/device/loadTableDevice';
$route['check-device-code-exists'] = 'master/device/checkCodeExists';
$route['check-device-sn-exists'] = 'master/device/checkSNExists';
$route['check-device-is-used'] = 'master/device/isDeviceUsed';

$route['master-employee'] = 'master/employee';
$route['checkout-cart/(:any)/(:any)'] = 'checkout_cart';
$route['generateQris'] = 'checkout_cart/generateQris';
$route['checkQris'] = 'checkout_cart/checkQris';
$route['checkout-cart/(:any)'] = 'checkout_cart/view_prospective/$1';
$route['master-prospective-employees'] = 'master/prospective_employees';
$route['master-prospective-employees/(:any)'] = 'master/prospective_employees/view_prospective/$1';
$route["employee-redistribute"] = "master/employee/redistribute";
$route["employee-redistribute-all"] = "master/employee/redistributeAll";
$route["employee-delete-all"] = "master/employee/deleteTempAll";
$route['employee-get-detail'] = 'master/employee/loadDetailEmployee';
$route['load-table-employee'] = 'master/employee/loadTableEmployee';
$route['save-employee'] = 'master/employee/saveEmployee';
$route['save-prospective-employees/(:any)'] = 'master/prospective_employees/saveEmployee/$1';
$route['save-prospective-employees-temp/(:any)'] = 'master/prospective_employees/saveEmployeeTemp/$1';
$route['accept-prospective-employees/(:any)'] = 'master/prospective_employees/acceptEmployee/$1';
$route['get-employee-edit'] = 'master/employee/getDataEdit';
$route['get-prospective_employees-edit'] = 'master/prospective_employees/getDataEdit';
$route['delete-employee/(:any)'] = 'master/employee/deleteEmployee/$1';
$route['delete-employee-intrax/(:any)/(:any)'] = 'master/prospective_employees/deleteEmployee/$1/$2';
$route['device-switch-license']  = 'master/device/switchLicense';
$route['employee-switch-license']= 'master/employee/switchLicense';
$route['ajax-get-employee-data']= 'master/employee/loadDTemployee';
$route['ajax-get-employee-prospective/(:any)']= 'master/prospective_employees/loadDTemployee/$1';
$route['employee-resign'] = 'master/employee/SetResign';
$route['checkAccountNoExist']    = 'master/employee/checkAccountNoExist';
$route['checkEmailNoExist']    = 'master/employee/checkEmailNoExist';
$route['checkEmailEmployees']    = 'master/employee/checkEmailNoExist';
$route['employee-mutation']      = 'transaction/mutation';
$route['load-table-mutation']    = 'transaction/mutation/loadTableMutation';
$route['ajax-get-mutation-data'] = 'transaction/mutation/loadDTMutation';
$route['load-employee-source']   = 'transaction/mutation/loadEmployeeSource';
$route['cancel-mutation'] = 'transaction/mutation/cancelMutation';
$route['save-mutation'] = 'transaction/mutation/saveMutation';
/// fingerprint machine handler routes
$route['iclock/cdata'] = 'api/cdata';
$route['iclock/getrequest'] = 'api/getrequest';
$route['iclock/devicecmd'] = 'api/devicecmd';

// update employee
$route['edit-employee/(:any)'] = 'master/employee/edit_employee/$1';
//$route['save-mutation'] = 'transaction/mutation/save_mutation';
$route['get-mutation-destination'] = 'transaction/mutation/getMutationDestination';

$route['dashboard-getdata-resign'] = 'dashboard/getDataResign';
$route['dashboard-getdata-mutation-in'] = 'dashboard/getDataMutationIn';
$route['dashboard-getdata-mutation-out']= 'dashboard/getDataMutationOut';
$route['dashboard-load-location-review']= 'dashboard/getLocationReview';

$route['log-transaction'] = 'transaction/transaction_log';
$route['transaction-log-load-transaction'] = 'transaction/transaction_log/loadTransaction';
$route['report-employee'] = 'report/employee_report';
$route['report-history-log'] = 'report/history_log';
$route['report-history-intrax'] = 'report/history_intrax';

$route['ajaxDtGetHistoryLog'] = 'report/history_log/loadDataLog';
$route['ajaxDtGetEmployeeReport'] = 'report/employee_report/loadDataEmployee';
$route['ajaxDtGetLogIntrax'] = 'report/history_intrax/loadDataLogIntrax';
$route['report-history-log/print'] = 'report/history_log/reportPrint';
$route['report-employee/print'] = 'report/employee_report/reportPrint';

$route['report-histori-log-peremployee'] = 'report/historylog_peremployee';

$route['report-employee-mutation'] = 'report/mutation_report';
$route['ajaxDtGetMutationReport']  = 'report/mutation_report/loadDataMutation';
$route['report-mutation/print'] = 'report/mutation_report/reportPrint';

$route['report-user-activity'] = 'report/user_activity';
$route['ajaxDtGetUserActivityReport'] = 'report/user_activity/loadDataActivity';
$route['report-user-activity/print']  = 'report/user_activity/reportPrint';

$route['report-employee-resign'] = 'report/employee_resign';

$route['employee-leave'] = 'transaction/trx_leave';
$route['employee-leave-file/(:any)'] = 'transaction/trx_leave/download_file/$1';
$route['employee-leave/export'] = 'transaction/trx_leave/toxlsx';
$route['employee-leave/delete'] = 'transaction/trx_leave/deleteData';

$route['departement'] = 'master/departement';
$route['departement-tree'] = 'master/departement/getTree';
$route['departement-submit'] = 'master/departement/savedepartement';
$route['departement-detail'] = 'master/departement/detaildata';
$route['departement-delete/(:any)'] = 'master/departement/deletedata/$1';

$route['active-period'] = 'master/period';
$route['active-period/submit'] = 'master/period/submitdata';
$route['active-period/delete/(:any)'] = 'master/period/deldata/$1';

$route['about-us'] = 'about';
$route['help'] = 'help';

$route['notification-open'] = 'setting/openNotif';
$route['notifications'] = 'notification';
$route['renew-session'] = 'auth/login/renewSession';
$route['change-language'] = 'language/changeLanguage';
$route['device-monitor'] = 'setting/device_monitor';
$route['import-master'] = 'master/import';
//$route['import-submit'] = 'master/import/importSubmit';
$route['import-area']   = 'master/import/importArea';
$route['import-branch'] = 'master/import/importBranch';
$route['import-employee'] = 'master/import/importEmployee';
$route['import-employee-intrax/(:any)'] = 'master/prospective_employees/importEmployeeIntrax/$1';
$route['import-photoprofile'] = 'master/employee/importPhotoprofile';
$route['import-photocompany'] = 'setting/importPhotocompany';
$route['update-registrationLink'] = 'registration_access/updateRegistration_link';
$route['import-device'] = 'master/import/importDevice';
$route['download-import-template'] = 'master/import/downloadTemplate';
$route['addons-placement/(:any)'] = 'license/setting_addons/addons_placement/$1';
$route['addons-placement/(:any)/(:any)'] = 'license/setting_addons/addons_placement/$1/$2';
$route['addons-placement/(:any)/(:any)/(:any)'] = 'license/setting_addons/addons_placement/$1/$2/$3';
$route['save-addons-allocation'] = 'license/setting_addons/saveAddonsPlacement';
$route['save-intrax-allocation'] = 'license/setting_addons/saveIntraxPlacement';

$route['report-employee-resign/print']  = 'report/employee_resign/printReport';

$route['rootaccess'] = 'root/device_monitor';

$route['rootaccess/fakegps-manager'] ='root/fakegps';
$route['rootaccess/fakegps-manager/add-new'] = 'root/fakegps/manage_fakegps';
$route['rootaccess/fakegps-manager/edit/(:any)'] = 'root/fakegps/manage_fakegps/$1';
$route['rootaccess/fakegps-manager/delete/(:any)'] = 'root/fakegps/delete/$1';
$route['rootaccess/save-fakegps'] = 'root/fakegps/save_fakegps';

$route['rootaccess-login'] = 'root/auth/login';
$route['rootaccess/device-monitor'] = 'root/device_monitor';
$route['rootaccess/device-monitor/(:any)'] = 'root/device_monitor/index/$1';
$route['root-getDeviceActivity'] = 'root/device_monitor/getDeviceActivity';
$route['rootaccess/list-device'] = 'root/device_control';
$route['suspendDevice'] = 'root/device_control/suspendDevice';
$route['unlockSuspendedDevice'] = 'root/device_control/unlockSuspendedDevice';
$route['rootaccess/menu-manager'] = 'root/menu_manager/sideMenu';
$route['rootaccess/menu-manager/(:num)'] = 'root/menu_manager/sideMenu/$1';
$route['rootaccess/menu-manager/delete-menu/(:num)'] = 'root/menu_manager/delete_menu/$1';
$route['rootaccess/admin-manager'] ='root/admin_manager';
$route['rootaccess/admin-manager/add-new'] = 'root/admin_manager/manage_admin';
$route['rootaccess/admin-manager/edit/(:any)'] = 'root/admin_manager/manage_admin/$1';
$route['rootaccess/admin-manager/delete/(:any)'] = 'root/admin_manager/delete/$1';
$route['rootaccess/save-admin'] = 'root/admin_manager/save_admin';
$route['rootaccess/error-log']  = 'root/error_log';
$route['rootaccess/error/download/(:any)'] = 'root/error_log/download/$1';
$route['rootaccess/company-manager'] = 'root/Company_manager';
$route['cancel-group-mutation'] = "transaction/mutation/cancelGroupMutation";

$route['report-unamed-log'] = "report/unamed_log";

