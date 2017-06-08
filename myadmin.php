<?php
/* TODO:
 - service type, category, and services  adding
 - dealing with the SERVICE_TYPES_vmware define
 - add way to call/hook into install/uninstall
*/
return [
	'name' => 'Vmware Vps',
	'description' => 'Allows selling of Vmware Server and VPS License Types.  More info at https://www.netenberg.com/vmware.php',
	'help' => 'It provides more than one million end users the ability to quickly install dozens of the leading open source content management systems into their web space.  	Must have a pre-existing cPanel license with cPanelDirect to purchase a vmware license. Allow 10 minutes for activation.',
	'module' => 'licenses',
	'author' => 'detain@interserver.net',
	'home' => 'https://github.com/detain/myadmin-vmware-vps',
	'repo' => 'https://github.com/detain/myadmin-vmware-vps',
	'version' => '1.0.0',
	'type' => 'licenses',
	'hooks' => [
		'function.requirements' => ['Detain\MyAdminVmware\Plugin', 'Requirements'],
		'licenses.settings' => ['Detain\MyAdminVmware\Plugin', 'Settings'],
		'licenses.activate' => ['Detain\MyAdminVmware\Plugin', 'Activate'],
		'licenses.change_ip' => ['Detain\MyAdminVmware\Plugin', 'ChangeIp'],
		'ui.menu' => ['Detain\MyAdminVmware\Plugin', 'Menu']
	],
];
