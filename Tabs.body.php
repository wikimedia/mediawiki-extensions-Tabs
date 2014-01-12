<?php
/**
 * This tag extension creates the <tabs> and <tab> tags for creating tab interfaces and toggleboxes on wiki pages.
 * 
 * @example Tabs/Tabs.examples.txt
 *
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 * 
 * @file
 */

/*Possible features to add:
 * Check if nested <tabs>es work perfectly
 * Dropdown menus with :hover and button:focus
 * Possibility of showing <tab> on multiple indexes, for things like <t i="1,2">foo <t i="1">bar</t><t i="2">baz</t></t><t i="3">quux</t>
 *		do this by just adding both .tab-content-1 and .tab-content-2 to the tab
 * Self-closing tab support, for tab defining at the top: <t n="a"/><t n="b"/> using $text===null
 * Use of a parser-function alternative for the <tab> tag: {{#tab:a|b|c|d}}, would be useful for inline things
 */

class Tabs {
	/**
	 * @var int $tabsCount Counts the index of the <tabs> tag on the page. Increments by 1 before parsing the tag.
	 * @var int $tabCount Counts the index of the <tab> tag on the page. Increments by 1 before parsing the tag.
	 */
	public static $tabsCount = 0;
	public static $tabCount = 0;
	
	/*
	 * @var int $toStyle Counts the maximum amount of <tab> tags used within a single <tabs> tag
	 * Its value is used to determine the amount of lines to be added to the dynamic stylesheet.
	 */
	var $toStyle = 0;
	// TODO: create global settings variable for maximum amount of tabs allowed
	
	/**
	 * @var int $nested Keeps track of whether the <tab> is nested within a <tabs> or not. Value is either 0 or 1.
	 * Will reset to 0 either when parsing recursive tags within <tab>, or when finished parsing recursive tags within <tabs>.
	 */
	public static $nested = false;
	
	/**
	 * @var array $tabNames Contains a list of the previously used tab names in that scope. 
	 * Will be reset before calling Parser->recursiveTagParse, and is returned to its previous value afterwards, to create this effect.
	 */
	public static $tabNames = array();
	
	/**
	 * @var array $labels List of labels to make.
	 * @example array(1 => 'Tab 1', 2 => 'defined name');
	 */
	public static $labels = array();
	
	/**
	 * Initiate the tags
	 * @param Parser &$parser
	 * @return boolean true
	 */
	public static function init( &$parser ) {
		$parser->setHook( 'tab', array( new self(), 'renderTab' ) );
		$parser->setHook( 'tabs', array( new self(), 'renderTabs' ) );
		return true;
	}
	
	/**
	 * Converts each <tab> into either a togglebox, or the contents of one tab within a <tabs> tag.
	 *
	 * @param string $input
	 * @param array $attr
	 * @param Parser $parser
	 * @return string
	 */
	public function renderTab($input, $attr = array(), $parser) {
		if ($this::$tabCount === 0) $this->insertCSSJS($parser, true); // init styles
		++$this::$tabCount;
		$names = &$this::$tabNames;
		// This next line allows for "tab defining" at the top of the <tabs> tag, so they can be referred to via only their index or name.
		if (preg_match("/^\s*$/", $input)) return ''; // don't add another tab content box when <tab> is empty.
		$nested = $this::$nested;
		// Default value for the tab's given index: index attribute's value, or else the index of the tab with the same name as name attribute, or else the tab index
		if (isset($attr['index']) && intval($attr['index']) <= count($names))
			$index = intval($attr['index']); // if the index is given, and it isn't greater than the current index + 1.
		elseif (isset($attr['name']) && array_search($attr['name'], $names) !== false)
			$index = array_search($attr['name'], $names) ; // if index is not defined, but the name is, use the index of the tabname.
		else {
			$index = count($names)+1; // index of this tab in this scope.
		}
		
		$classPrefix = '';
		if ($nested) // Note: This is defined seperately for toggleboxes, because of the different classes required.
			$classPrefix .= "tabs-content tabs-content-$index";
		
		if (!isset($attr['class']))
			$attr['class'] = $classPrefix; // only the prefix if no classes have been defined
		else
			$attr['class'] = trim("$classPrefix ".htmlspecialchars($attr['class']));
		
		if (array_key_exists($index-1, $names)) // if array $names already has a name defined at position $index, use that
			$name = $names[$index-1];
		else // otherwise, use the entered name, or the $index with a "Tab " prefix if it is not defined or empty.
			$name = trim(isset($attr['name']) && trim($attr['name']) ? $attr['name'] : wfMessage('tabs-tab-label-placeholder', $index));

		if (!$nested) { // This runs when the tab is not nested inside a <tabs> tag.
			$nameAttrs = array(
				'name'=>isset($attr['name']),
				'openname'=>isset($attr['openname']),
				'closename'=>isset($attr['closename']),
			);
			$checked = $this->checkAttrIsDefined('collapsed', $attr) ? '' : ' checked="checked"';
			$id = str_replace(" ", "_", wfMessage('tabs-tab-label-placeholder', $this::$tabCount));
			
			/*
			 * If only one of the openname and closename attributes is defined, the both will take the defined one's value
			 * If neither is defined, but the name attribute is, both will take the name attribute's value
			 * If all three are undefined, the default "Show/Hide content" will be used
			 */
			if ($nameAttrs['openname'] && $nameAttrs['closename']) {
				$openname = htmlspecialchars($attr['openname']);
				$closename = htmlspecialchars($attr['closename']);
			} elseif ($nameAttrs['openname'] && !$nameAttrs['closename']) $openname = $closename = htmlspecialchars($attr['openname']);
			elseif ($nameAttrs['closename'] && !$nameAttrs['openname']) $openname = $closename = htmlspecialchars($attr['closename']);
			elseif (!$nameAttrs['openname'] && !$nameAttrs['closename'] && $nameAttrs['name']) $openname = $closename = htmlspecialchars($attr['name']);
			elseif (!$nameAttrs['openname'] && !$nameAttrs['closename']) {
				$openname = wfMessage('tabs-toggle-open-placeholder');
				$closename = wfMessage('tabs-toggle-close-placeholder');
			}
			
			// Check if the togglebox should be displayed inline. No need to check for the `block` attribute, since the default is display:block;
			$inline = $this->checkAttrIsDefined('inline', $attr) ? ' tabs-inline' : '';
			$label = "<input class=\"tabs-input\" type=\"checkbox\" id=\"$id\"$checked/><label class=\"tabs-label\" for=\"$id\"><span class=\"tabs-open\">$openname</span><span class=\"tabs-close\">$closename</span></label>";
			$attr['class'] = "tabs tabs-togglebox$inline ".$attr['class'];
			$attrStr = $this->getSafeAttrs($attr);
			$container = array(
				"<div$attrStr><div class=\"tabs-container\">$label",
				'</div></div>'
			);
			$containerStyle = '';
			if (isset($attr['container'])) $containerStyle = htmlspecialchars($attr['container']);
			$attrStr = " class=\"tabs-content\" style=\"$containerStyle\""; //the attrStr is used in the outer div, so only the containerStyle should be applied to the content div.
		} else { // this runs when the tab is nested inside a <tabs> tag.
			$container = array('', '');
			if (array_search($name, $names) === false) // append name if it's not already in the list.
				$names[] = $name;
			if ($this->checkAttrIsDefined('inline', $attr))
				$ib = 'tabs-inline';
			else if ($this->checkAttrIsDefined('block', $attr))
				$ib = 'tabs-block';
			else
				$ib = '';
			$attr['class'] = "$ib ".$attr['class'];
			$attrStr = $this->getSafeAttrs($attr);
			$this::$labels[intval($index)] = $name; // Store the index and the name so this can be used within the <tabs> hook to create labels
		}

		$this::$nested = false; // temporary
		$newstr = $parser->recursiveTagParse($input);
		$this::$nested = $nested; // revert
		return $container[0]."<div$attrStr>$newstr</div>".$container[1];
	}
	
	/**
	 * Converts each <tabs> to a tab layout.
	 *
	 * @param string $input
	 * @param array $attr
	 * @param Parser $parser
	 * @return string
	 */
	public function renderTabs($input, $attr = array(), $parser) {
		if ($this::$tabsCount === 0) $this->insertCSSJS($parser, true); // init styles
		$count = ++$this::$tabsCount;
		$attr['class'] = isset($attr['class']) ? 'tabs tabs-tabbox '.$attr['class'] : 'tabs tabs-tabbox';
		$attrStr = $this->getSafeAttrs($attr);
		$containerStyle = '';
		if (isset($attr['container'])) $containerStyle = htmlspecialchars($attr['container']);
		
		// CLEARING:
		$tabnames = $this::$tabNames; // Copy this array's value, to reset it to this value after parsing the inner <tab>s.
		$this::$tabNames = array(); // temporarily clear this array, so that only the <tab>s within this <tabs> tag are tracked.
		$this::$labels = array(); // Reset after previous usage
		$this::$nested = true;
		// PARSING
		$newstr = $parser->recursiveTagParse($input);
		// AND RESETTING (to their original values):
		$this::$tabNames = $tabnames; // reset to the value it had before parsing the nested <tab>s. All nested <tab>s are "forgotten".
		$this::$nested = false; // reset
		
		/**
		 * The default value for $labels creates a seperate input for the default tab, which has no label attached to it.
		 * This is to allow any scripts to be able to check easily if the user has changed the shown tab at all,
		 * by checking if this 0th input is checked.
		 */
		$labels = "<input type=\"radio\" id=\"tabs-input-$count-0\" name=\"$count\" class=\"tabs-input tabs-input-0\" checked>";
		$indices = array(); // this is to most accurately count the amount of <tab>s in this <tabs> tag.
		foreach ($this::$labels as $i => $n) {
			$indices[] = $i;
			$labels .= $this->makeLabel($i, $n);
		}
		
		$toStyle = &$this->toStyle;
		if ($toStyle < count($indices)) { // only redefine the styles to be added to the head if we actually need to generate extra styles.
			$toStyle = count($indices);
			$this->insertCSSJS($parser); // reload dynamic CSS with new amount
		}
		
		return "<div$attrStr>$labels<div class=\"tabs-container\" style=\"$containerStyle\">$newstr</div></div>";
	}
		
	/**
	 * Check if an attribute is defined, and doesn't have a false value ("0", "false" or "null")
	 * @param string|int $attr The key to check for
	 * @param array $obj The array of attributes to search through
	 * @return boolean true when the attribute is defined, its boolean value is not false, and not "0", "false" or "null"; false otherwise.
	 */
	public function checkAttrIsDefined($attr, $obj) {
		return isset($obj[$attr]) && $obj[$attr] && array_search(trim($obj[$attr]), array('0', 'false', 'null')) === false;
	}
	
	/**
	 * Template for the tab label
	 * @param int $tabN The index of the individual tab.
	 * @param string $label The label that is going to appear to the user.
	 * @return string HTML code of the label
	 */
	public function makeLabel($tabN, $label) {
		$tagN = $this::$tabsCount;
		$label = htmlspecialchars($label);
		$id = str_replace(" ", "_", $label);// for direct linking to a tab
		return "<input type=\"radio\" id=\"tabs-input-$tagN-$tabN\" name=\"$tagN\" class=\"tabs-input tabs-input-$tabN\"><label class=\"tabs-label\" for=\"tabs-input-$tagN-$tabN\" id=\"$id\" data-tabpos=\"$tabN\">$label</label>";
	}
	
	/**
	 * Filters list of entered parameters to only the HTML-safe attributes
	 * @param array $attr The full list of entered attributes
	 * [@param array $safe] The array in which to store the safe attributes
	 * @return array The list of safe attributes. Format: array(attrname => attrvalue)
	 */
	public function getSafeAttrs($attr, &$safe = array()) {
		$safeAttrs = array('class', 'id', 'title', 'style');
		$attrStr = '';
		// Apply width style if width attribute is defined, and no styles are defined OR width is not defined in the styles.
		$widhigh = array('width', 'height');
		foreach ($widhigh as $i) {
			$setStyles = isset($attr['style']) ? $attr['style'] : false;
			// If the attribute 'width' or 'height' is defined, AND either no styles have yet been set, OR those set stiles have no defined 'width' or 'height'.
			if (isset($attr[$i]) && (!$setStyles || !preg_match("/$i\s*:/", $setStyles))) {
				$whAttr = $attr[$i];
				$whAttr .= preg_match("/^\d+$/", $whAttr) ? 'px' : ''; // append px when no unit is defined
				// insert the 'width' or 'height' style at the start of the styles to prevent having to insert a semicolon at the end of the current style list, and possibly getting double semicolons.
				$attr['style'] = "$i: $whAttr; $setStyles";
			}
		}

		foreach ($safeAttrs as $i) {
			if (array_key_exists($i,$attr)) {
				$safe[$i] = htmlspecialchars(trim($attr[$i]));
				$attrStr .= " $i=\"".$safe[$i].'"';
			} else
				$safe[$i] = '';
		}
		return $attrStr;
	}
		
	/**
	 * Insert the static and dynamic CSS and JS into the page
	 * @param Parser $parser
	 * @param boolean $firstrun Set to true if the initial scripts need to be loaded. Default: only the dynamic styles are refreshed.
	 */
	public function insertCSSJS($parser, $firstrun = false) {
		$parserOutput = $parser->getOutput();
		$parserOutput->addHeadItem($this->createDynamicCss(), 'TabsStyles');
		if ($firstrun) {
			$parserOutput->addModuleStyles('ext.tabs');
			$parserOutput->addModuleScripts('ext.tabs');
		}
	}

	public function createDynamicCss() {
		// TODO: add <form> using prependHTML() for the form="" attribute of inputs, for HTML5 semantic compatibility
		$css = "<style type=\"text/css\">/*<![CDATA[*/\n/* Dynamically generated tabs styles */\n";
		for ($i=1;$i<=$this->toStyle;$i++) {
			$css .= ".tabs-input-$i:checked ~ .tabs-container .tabs-content-$i,\n";
		}
		$css .= '.tabs-input-0:checked ~ .tabs-container .tabs-content-1 {display:inline-block;}';
		return "$css\n/*]]>*/</style>";
	}
}