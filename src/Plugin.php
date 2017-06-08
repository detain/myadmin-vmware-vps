<?php

namespace Detain\MyAdminVmware;

use Detain\Vmware\Vmware;
use Symfony\Component\EventDispatcher\GenericEvent;

class Plugin {

	public function __construct() {
	}

	public static function Activate(GenericEvent $event) {
		// will be executed when the licenses.license event is dispatched
		$license = $event->getSubject();
		if ($event['category'] == SERVICE_TYPES_FANTASTICO) {
			myadmin_log('licenses', 'info', 'Vmware Activation', __LINE__, __FILE__);
			function_requirements('activate_vmware');
			activate_vmware($license->get_ip(), $event['field1']);
			$event->stopPropagation();
		}
	}

	public static function ChangeIp(GenericEvent $event) {
		if ($event['category'] == SERVICE_TYPES_FANTASTICO) {
			$license = $event->getSubject();
			$settings = get_module_settings('licenses');
			$vmware = new Vmware(FANTASTICO_USERNAME, FANTASTICO_PASSWORD);
			myadmin_log('licenses', 'info', "IP Change - (OLD:".$license->get_ip().") (NEW:{$event['newip']})", __LINE__, __FILE__);
			$result = $vmware->editIp($license->get_ip(), $event['newip']);
			if (isset($result['faultcode'])) {
				myadmin_log('licenses', 'error', 'Vmware editIp('.$license->get_ip().', '.$event['newip'].') returned Fault '.$result['faultcode'].': '.$result['fault'], __LINE__, __FILE__);
				$event['status'] = 'error';
				$event['status_text'] = 'Error Code '.$result['faultcode'].': '.$result['fault'];
			} else {
				$GLOBALS['tf']->history->add($settings['TABLE'], 'change_ip', $event['newip'], $license->get_ip());
				$license->set_ip($event['newip'])->save();
				$event['status'] = 'ok';
				$event['status_text'] = 'The IP Address has been changed.';
			}
			$event->stopPropagation();
		}
	}

	public static function Menu(GenericEvent $event) {
		// will be executed when the licenses.settings event is dispatched
		$menu = $event->getSubject();
		$module = 'licenses';
		if ($GLOBALS['tf']->ima == 'admin') {
			$menu->add_link($module, 'choice=none.reusable_vmware', 'icons/database_warning_48.png', 'ReUsable Vmware Licenses');
			$menu->add_link($module, 'choice=none.vmware_list', 'icons/database_warning_48.png', 'Vmware Licenses Breakdown');
			$menu->add_link($module.'api', 'choice=none.vmware_licenses_list', 'whm/createacct.gif', 'List all Vmware Licenses');
		}
	}

	public static function Requirements(GenericEvent $event) {
		// will be executed when the licenses.loader event is dispatched
		$loader = $event->getSubject();
		$loader->add_requirement('crud_vmware_list', '/../vendor/detain/crud/src/crud/crud_vmware_list.php');
		$loader->add_requirement('crud_reusable_vmware', '/../vendor/detain/crud/src/crud/crud_reusable_vmware.php');
		$loader->add_requirement('get_vmware_licenses', '/../vendor/detain/myadmin-vmware-vps/src/vmware.inc.php');
		$loader->add_requirement('get_vmware_list', '/../vendor/detain/myadmin-vmware-vps/src/vmware.inc.php');
		$loader->add_requirement('vmware_licenses_list', '/../vendor/detain/myadmin-vmware-vps/src/vmware_licenses_list.php');
		$loader->add_requirement('vmware_list', '/../vendor/detain/myadmin-vmware-vps/src/vmware_list.php');
		$loader->add_requirement('get_available_vmware', '/../vendor/detain/myadmin-vmware-vps/src/vmware.inc.php');
		$loader->add_requirement('activate_vmware', '/../vendor/detain/myadmin-vmware-vps/src/vmware.inc.php');
		$loader->add_requirement('get_reusable_vmware', '/../vendor/detain/myadmin-vmware-vps/src/vmware.inc.php');
		$loader->add_requirement('reusable_vmware', '/../vendor/detain/myadmin-vmware-vps/src/reusable_vmware.php');
		$loader->add_requirement('class.Vmware', '/../vendor/detain/vmware-vps/src/Vmware.php');
		$loader->add_requirement('vps_add_vmware', '/vps/addons/vps_add_vmware.php');
	}

	public static function Settings(GenericEvent $event) {
		// will be executed when the licenses.settings event is dispatched
		$settings = $event->getSubject();
		$settings->add_text_setting('licenses', 'Vmware', 'vmware_username', 'Vmware Username:', 'Vmware Username', $settings->get_setting('FANTASTICO_USERNAME'));
		$settings->add_text_setting('licenses', 'Vmware', 'vmware_password', 'Vmware Password:', 'Vmware Password', $settings->get_setting('FANTASTICO_PASSWORD'));
		$settings->add_dropdown_setting('licenses', 'Vmware', 'outofstock_licenses_vmware', 'Out Of Stock Vmware Licenses', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_LICENSES_FANTASTICO'), array('0', '1'), array('No', 'Yes', ));
	}

}
