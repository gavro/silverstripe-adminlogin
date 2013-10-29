<?php
/**
 * Custom Admin Login form screen
 */
 
class AdminLoginExtension extends Extension {
	
	// redirect to AdminSecurity, when we are coming from /admin/*
	function onBeforeSecurityLogin() {
		if(isset($_GET['BackURL']) && strstr($_GET['BackURL'], '/admin/')) {
			if(Controller::curr()->class != 'AdminSecurity') {
				$link = 'AdminSecurity/login' . '?BackURL=' . urlencode($_GET['BackURL']);
				return $this->owner->redirect($link);
			}
		}
	}
}

/**
 * Dummy Controller to prevent loading frontend css and javscript files
 */
class AdminLoginPage_Controller extends ContentController {
	
}

class AdminSecurity extends Security {
	
	private static $allowed_actions = array( 
		'passwordsent'
	);
	
	/**
	 * Template thats used to render the pages.
	 *
	 * @var string
	 */
	private static $template_main = 'AdminLogin';
	
	public function init() {
		parent::init();
		// this prevents loading frontend css and javscript files
		Object::useCustomClass('Page_Controller','AdminLoginPage_Controller');
		Object::useCustomClass('MemberLoginForm','AdminLoginForm');
		Requirements::css('adminlogin/css/style.css');
	}
	
	public function Link($action = null) {
		return "AdminSecurity/$action";
	}
	
	public static function isAdminLogin() {
		return strstr(self::getBackUrl(), '/admin/');
	}
	
	public static function getBackUrl() {
		if(isset($_REQUEST['BackURL'])) {
			return $_REQUEST['BackURL'];
		}elseif(isset($_SESSION['BackURL'])) {
			return $_SESSION['BackURL'];
		}
	}
	
	public function passwordsent($request) {
		return parent::passwordsent($request);
	}
	
	/**
	 * @see Security::getPasswordResetLink()
	 * We overload this, so we can add the BackURL to the password resetlink
	 */
	public static function getPasswordResetLink($member, $autologinToken) {
		$autologinToken = urldecode($autologinToken);		
		$selfControllerClass = __CLASS__;
		$selfController = new $selfControllerClass();
		return $selfController->Link('changepassword') . "?m={$member->ID}&t=$autologinToken";
	}
	
	public function ChangePasswordForm() {
		return new ChangePasswordForm($this, 'ChangePasswordForm');
	}
}