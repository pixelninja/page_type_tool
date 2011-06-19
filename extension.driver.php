<?php

	Class extension_page_type extends Extension{
	
		public function about(){
			return array(
				'name' => 'Page Type',
				'version' => '1.0',
				'release-date' => '2011-06-19',
				'author' => array(
				 		'name' => 'Phill Gray',
						'email' => 'phill@randb.com.au'
					)
		 		);
		}
		
		/*public function fetchNavigation() {
			return array(
				array(
					'location' => 'Blueprints',
					'name'	=> 'Sitemap XML',
					'link'	=> '/xml/',
				),
			);
		}*/
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => '__appendPreferences'
				),
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'initaliseAdminPageHead'
				)
			);
		}
		
		/*public function install() {
			// Add defaults to config.php
			if (!Symphony::Configuration()->get('index_type', 'sitemap_xml')) {
				Symphony::Configuration()->set('index_type', 'index', 'sitemap_xml');
				Symphony::Configuration()->set('global', 'sitemap', 'sitemap_xml');
				Symphony::Configuration()->set('lastmod', date('c', time()), 'sitemap_xml');
				Symphony::Configuration()->set('changefreq', 'monthly', 'sitemap_xml');
			}
			
			// Add table to database 
			Symphony::Database()->query('
				CREATE TABLE IF NOT EXISTS tbl_sitemap_xml (
					`id` INT(4) UNSIGNED DEFAULT NULL AUTO_INCREMENT,
					`page_id` INT(4) UNSIGNED DEFAULT NULL,
					`datasource_handle` VARCHAR(255) DEFAULT NULL,
					`relative_url` TINYTEXT DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY datasource_handle_page_id (`datasource_handle`, `page_id`)
				) ENGINE=MyISAM
			');
			
			// Autogenerate a blank sitemap.xml
			$fp = fopen(getcwd() . '/sitemap.xml', 'w+');
			fclose($fp);
			
			return Administration::instance()->saveConfig();
		}*/
		
		/*public function uninstall() {
			Symphony::Configuration()->remove('sitemap_xml');
			Symphony::Database()->query('DROP TABLE IF EXISTS tbl_sitemap_xml');
			return Administration::instance()->saveConfig();
		}*/
		
		public function initaliseAdminPageHead($context) {
			$callback = Symphony::Engine()->getPageCallback();
			
			// Append assets
			if($callback['driver'] == 'systempreferences') {
				Symphony::Engine()->Page->addScriptToHead(URL . '/extensions/sitemap_xml/assets/sitemap_xml.ajax.js', 10001);
			}
		}
		
		public function __appendPreferences($context) {
			$pages = Symphony::Database()->fetch("SELECT p.* FROM `tbl_pages` AS p ORDER BY p.sortorder ASC");
		
			
			/*@group Fieldset containing Page Type settings*/
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings add_pagetype');
			$group->appendChild(new XMLElement('legend', __('Add page type'))); 
			
			$span = new XMLElement('span', NULL, array('class' => 'frame'));
			
			
			$page_list = array('');
			foreach($pages as $page) {
				$page_types = Symphony::Database()->fetchCol('type', "SELECT `type` FROM `tbl_pages_types` WHERE page_id = '".$page['id']."' ORDER BY `type` ASC");
				$page['types'] = $page_types;
				
				$parent = null;
				if($page['parent'] != null) {
					$parent = Symphony::Database()->fetch("SELECT p.* FROM `tbl_pages` AS p WHERE p.id =".$page['parent']);
					$parent = $parent[0]['title'].': ';
				}
				
				$page_list[] = array(
					$page['id'], false, $parent.$page['title']
				);
				
				$this->_pages[] = $page;
			}
			
			$label = Widget::Label(__('Pages'));
			$select = Widget::Select('addtype[page][]', $page_list, array('multiple'=>'multiple'));
			$label->appendChild($select);
			$group->appendChild($label);
			
			$label = Widget::Label(__('Type to add to selected pages:'));
			$label->appendChild(Widget::Input('addtype[page_type]', 'high'));
			$group->appendChild($label);
			
			$span->appendChild(new XMLElement('button', __('Add type to pages'), array_merge(array('name' => 'action[add_pagetype]', 'type' => 'submit'))));
	
			$group->appendChild($span);
			$context['wrapper']->appendChild($group);
			/*@group end*/

			/*@group mysql query on Type submit*/
			if(isset($_REQUEST['action']['add_pagetype'])){
				$id = $_REQUEST['addtype']['page'];
				$type = $_REQUEST['addtype']['page_type'];
				
				foreach($id as $page) {
					Symphony::Database()->query('
						INSERT INTO tbl_pages_types VALUES ("", "'.$page.'", "'.$type.'")
					');
				}
			}			
		}
	}

?>