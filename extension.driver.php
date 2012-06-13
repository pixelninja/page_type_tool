<?php

	Class extension_page_type_tool extends Extension{
	
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'appendPreferences'
				),
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'initaliseAdminPageHead'
				)
			);
		}
				
		public function initaliseAdminPageHead($context) {
			$callback = Administration::instance()->getPageCallback();
			
			// Append assets
			if($callback['driver'] == 'systempreferences') {
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/page_type_tool/assets/page_type_tool.ajax.js', 10001);
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/page_type_tool/assets/page_type_tool.publish.css', 'screen');
			}
		}
		
		public function appendPreferences($context) {
			$pages = Symphony::Database()->fetch("SELECT p.* FROM `tbl_pages` AS p ORDER BY p.sortorder ASC");
		
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