<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		/** Admin Module */
		$admin = new RouteList('Admin');

		/** Our Menu */
		$admin[] = new Route('admin/nase-menu/obsah-menu', 'OurMenu:all');
		$admin[] = new Route('admin/nase-menu', 'OurMenu:all');
		$admin[] = new Route('admin/nase-menu/pridat-do-menu', 'OurMenu:add');

		/** Orders */
		$admin[] = new Route('admin/objednavky', 'Orders:all');
		$admin[] = new Route('admin/objednavky/detail/<id>', 'Orders:detail');

		/** Resources */
		$admin[] = new Route('admin/suroviny[/<resource>]', 'Resources:resource');
		$admin[] = new Route('admin/suroviny/upravit[/<resourceId>]', 'Resources:edit');

		/** Users */
		$admin[] = new Route('admin/uzivatele[/<role>]', 'Users:all');

		/** Admin */
		$admin[] = new Route('admin/dashboard', 'Dashboard:default');
		$admin[] = new Route('admin/', 'Dashboard:default', Route::ONE_WAY);

		/** Default Admin Routes */
		$admin[] = new Route('admin/<presenter>/<action>[/<id>]', 'Dashboard:default');


		/** Web Mobule */
		$web = new RouteList('Web');

		$web[] = new Route('nase-menu/', 'Homepage:ourMenu');
		$web[] = new Route('vlastni-bageta/', 'Builder:default', Route::ONE_WAY);
		$web[] = new Route('vlastni-bageta/uvod', 'Builder:default');
		$web[] = new Route('kosik', 'Cart:default');

		/** My Account */
		$web[] = new Route('muj-ucet', 'MyAccount:default', Route::ONE_WAY);
		$web[] = new Route('muj-ucet/objednavky', 'MyAccount:default');
		$web[] = new Route('muj-ucet/detail-objednavky/<orderId>', 'MyAccount:order');
		$web[] = new Route('muj-ucet/osobni-udaje', 'MyAccount:details');
		$web[] = new Route('muj-ucet/zmena-hesla', 'MyAccount:changePassword');

		/** Baguette Builder */
		$web[] = new Route('vlastni-bageta/sestaveni/krok/<step>', 'Builder:compilation');

		/** Not Supported */
		$web[] = new Route('mobilni-aplikace', 'NotSupported:default');

		/** Default Web Routes */
		$web[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		$web[] = new Route('www/', 'Homepage:default');

		$router = new RouteList;
		$router[] = $admin;
		$router[] = $web;

		return $router;
	}
}