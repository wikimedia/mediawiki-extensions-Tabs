This page contains documentation and demos for the tabs extension.
Paste this text on a wiki article with the tabs extension installed
to view these demos.

-----

{{TOC|limit=4}}

== Installation ==
{{ExtensionInstall|Tabs}}

== Configuration ==

This extension has no configuration options in <code>LocalSettings.php</code>, but it does have the ''MediaWiki:tabs-dropdown-bgcolor'' message associated with it, which is not meant to be translated. This message contains the default value for the <code>background-color</code> style for dropdown menus. This needs to be a [https://developer.mozilla.org/en-US/docs/Web/CSS/color_value valid <code>background-color</code> value].

It also has the following internationalisation messages associated with it:

*''MediaWiki:tabs-tab-label'' - The default label for a tab. The <code>$1</code> stands for the index of the tab.
*''MediaWiki:tabs-toggle-open'' - The default opening label for toggle boxes.
*''MediaWiki:tabs-toggle-close'' - The default closing label for toggle boxes.
*''MediaWiki:tabs-dropdown-label'' - The default label for a dropdown menu.

== Usage ==

=== General usage information ===

'''Note:''' - This extension uses the <code>bgcolor</code> attribute for dropdown menus. This is in no way meant as encouragement for the use of this deprecated attribute anywhere other than this tag.

For both the <code>&lt;tab&gt;</code> and <code>&lt;tab&gt;</code> tags, parser functions ''can'' be used within the content of the tag, but ''not'' in the attributes. To use parser functions within the attributes, the <code>#tag:tabs</code> or <code>#tag:tab</code> parser functions should be used. The [[#Parser function|<code>#tab</code> parser function]] will also work, but since the only attributes it can define are the <code>index</code> and <code>name</code> attributes, these don't allow complete support.

For example, this will not work:

<pre>
<tabs style="color:{{#if:{{{1|}}}|green|red}}">
<tab name="{{{1|}}}">Foo</tab>
<tab name="{{{2|}}}">Bar</tab>
</tabs>
</pre>
But this will work:
<pre>
{{#tag:tabs|
{{#tag:tab|Foo|name={{{1|}}}}}
{{#tab:{{{2|}}}|Bar}}
|style=color:{{#if:{{{1|}}}|green|red}} }}
</pre>

=== Toggle box ===

==== Documentation ====
You can create a simple collapsible box by enclosing some content between <code><nowiki><tab> ... </tab></nowiki></code>. All content within the tags will be displayed within the toggle box.

===== Available attributes: =====
*<code>collapsed</code> - If this attribute is set, the toggle box will appear collapsed when the page loads. Otherwise it will be opened.
*<code>inline</code> - If this attribute is set, the toggle box can be placed within text without interrupting the flow of the text.
*<code>dropdown</code> - See [[#Dropdown menus]].
*Name attributes:
**<code>openname</code> - The label for the toggle box that indicates that clicking it will close the box. Default value is stored in the ''MediaWiki:tabs-toggle-open'' page.
**<code>closename</code> - Same as <code>openname</code>, but for closing the toggle box. Default is stored in ''MediaWiki:tabs-toggle-close''.
**<code>name</code> - If neither the <code>openname</code> and <code>closename</code> is defined, this value will be used for both states.
**If only one of the <code>openname</code> or <code>closename</code> attributes is defined, the other will take its value. If neither is defined, and the <code>name</code> attribute is also not defined, the default values are taken from the respective MediaWiki pages.
*<code>container</code> - Use this attribute to define any styles for the toggle box container. Styles defined here will only affect the content of the toggle box, not the label.
*Default HTML attributes:
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#title <code>title</code>] - Determines the tooltip shown when hovering over the box.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#style <code>style</code>] - Use this attribute to define any styles for the box. This can also affect the box's label.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#class <code>class</code>] - Adds classes to the box.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#id <code>id</code>] - Adds an id to the box. This id must be unique on the page, as with any id.

==== Toggle box demos ====

===== Plain toggle box =====
<tab>This toggle box has no attributes assigned to it.</tab>

===== Toggle box attributes =====
<tab collapsed openname="Toggle" style="font-weight:bold;width:500px;" container="font-style:italic;" title="Example tooltip">
This toggle box has the following attributes defined:
*<code>collapsed</code> - By default, it is closed.
*<code>openname="Toggle"</code> - The label will show "Toggle" when it can be clicked to open the box. Since no <code>closename</code> attribute is defined, it defaults to "Toggle" too.
*<code>style="font-weight:bold;width:500px;"</code> - The whole toggle box will be bold, and have a width of 500px.
*<code>container="font-style:italic;"</code> - Only the contents of the toggle box will be italic.
*<code>title="Example tooltip"</code> - The tooltip that shows when hovering over this tab is defined via the <code>title</code> attribute.
</tab>

===== Inline toggle boxes =====
Here is an example of an inline toggle box. <tab inline collapsed openname="Show" closename="Hide">This togglebox is <code>inline</code> and <code>collapsed</code></tab> This toggle box has the attributes <code>openname="Show"</code> and <code>closename="Hide"</code> to change the default label text.

=== Dropdown menus ===
==== Documentation ====

Dropdown menus are made by simply defining the <code>dropdown</code> attribute on a toggle box. They can be opened by either hovering over the label, or by clicking on the label to keep it opened even after moving away the cursor. Dropdown menus have an opening delay of 0.2 seconds built in to prevent accidental opening when hovering over the label, and to prevent accidental closing when accidentally moving the cursor off the dropdown. This delay is enough to prevent accidents like those, but is not enough to be bothersome.

Dropdown menus are heavily based on the code for toggle boxes, so will also resemble them in many ways. There are a couple of quite distinct differences though.

Since dropdown menus use the <code>&lt;menu&gt;</code> tag for their content, it is permitted to use <code>&lt;li&gt;</code> tags directly within the dropdown menu's contents. Any other content is also allowed.

Dropdown menus will convert all list items and links placed within to specially styled list items. The only exception is that links show as they normally do when placed within unordered lists ([http://www.mediawiki.org/wiki/Help:Lists any line starting with <code>*</code>]). This is also the only difference between ordered and unordered lists.

Any nested lists will be rendered as sub-menus in the dropdown menu. Nested lists are created by starting a line with [http://www.mediawiki.org/wiki/Help:Lists multiple <code>*</code> or <code>#</code> characters]. There is one limitation with this however: Individual nested lists can not alternate between ordered and unordered lists. Seperate levels can, however. For example, this is not allowed:
<pre>
*Menu item 1
*Menu item 2
**Sub-menu item 1
*#Sub-menu item 2
</pre>
But this is:
<pre>
*Menu item 1
#Menu item 2
#*Sub-menu item 1
#*Sub-menu item 2
#*#Sub-sub-menu item 1
</pre>

===== Available attributes: =====

*All attributes that are available for toggle boxes
*<code>dropdown</code> - Must be defined for the toggle box to become a dropdown menu.
*<code>openname</code> and <code>closename</code> - These attributes are identical to the <code>name</code> attribute in dropdown menus. It is not possible to let the dropdown switch between 2 values. If the <code>openname</code> attribute is set, that value will be used as label, otherwise the <code>closename</code> value is used, and if neither of those values is set, the <code>name</code> value is used.
*<code>bgcolor</code> - Because of how the background-color styling for dropdown works (background styles are applied to all items within dropdowns, otherwise they would become transparent), background colors need to be defined seperately. This must be done in the <code>bgcolor</code> attribute. This attribute works exactly the same as the <code>background-color</code> style in CSS. This defaults to the value defined in ''MediaWiki:tabs-dropdown-bgcolor''.

==== Dropdown demos ====

===== Dropdown without lists =====
<tab dropdown style="width:300pt" openname="Click/hover to show" closename="Showing...">
This dropdown contains no lists, so it will not have any of the styling designed for dropdowns. It does work as it normally would though.

This dropdown also has its <code>style</code> attribute set to <code>style="width:300pt"</code>. It also has different <code>openname</code> and <code>closename</code> attributes, so it defaults to the <code>openname</code> value.
</tab>

===== Background-color for dropdowns =====

<tab dropdown bgcolor="salmon}body{font-weight:bold;">
*This tab has a its <code>bgcolor</code> attribute set to <code>bgcolor="salmon"</code>. Just defining a <code>background-color</code> style would not work.
</tab>

===== Lists and links =====
Here you can see the difference between unordered and ordered lists within dropdowns. The appearance of both does not change, but the behaviour of links within them does.
<tab dropdown>
#The first 2 items use ordered lists, which will show links as list items too.
#[[#Dropdown demos|Example link]]
*From here on this dropdown uses ordered lists, so links are shown within text.
*See this [[#Dropdown demos|example link]].
*Any links in dropdown menus placed outside lists will also be rendered as list items, like the following link:
[[#Dropdown demos|Example link]]
</tab>

===== Inline dropdowns =====
It is also possible to create inline dropdowns: <tab dropdown inline>
*You can do anything you'd normally do in a dropdown
*This box will fit in with the text.
</tab>. This will also not interrupt the flow of the text.

===== Nested lists =====

<tab dropdown>
*This dropdown menu demonstrates dropdown menus with multiple levels.
*Hovering over a list item with further lists nested within it will cause the next level to show up
*Hover over this item to see
**This list now shows up.
**Nested lists can also contain even more lists
**See this item for example
***This is a third level menu
**This can go on for any amount of levels.
*Multiple sub-menus are also allowed
**Such as this one.
</tab>

===== Alternating ordered and unordered lists =====

<tab dropdown>
*It is possible to alternate between ordered and unordered lists, but not within sub-menus.
*The first 2 items are unordered list items
#And this is an ordered list item
#*This is an unordered list item again
#*This also ''has'' to be an unordered list item
#*#This can be an ordered list item again though
#*#But then this also has to be ordered.
#*Within an individual sub-menu, it is not possible to change between ordered and unordered list items
</tab>

<tab dropdown>
*This item is in an unordered list, so it allows [[#Dropdown demos|in-line linking]].
#This item and the next one are in an ordered list, so they turn links into list-items
#[[#Dropdown demos|List-item links]]
#*...with a sub-menu that uses unordered lists again, so allows [[#Dropdown demos|in-line linking]] again.
#*#And this sub-menu again creates...
#*#[[#Dropdown demos|...list-item links]]
</tab>

=== Tab menus ===
==== Documentation ====

Tab menus can be used to make it possible to switch between different layouts. Anything within <code><nowiki><tabs> ... </tabs></nowiki></code> tags is rendered as a tab menu. Individual tabs are then defined via a <code>&lt;tab&gt;</code> tag.

===== Available attributes =====
;<code>&lt;tabs&gt;</code>
*<code>container</code> - Use this attribute to define any styles for the tabs container. Styles defined here will only affect the container of the tabs, not the labels.
*<code>plain</code> - If this attribute is set, the tab interface will be a much more plain layout, without a border around the container, and with the tab labels just being buttons above it, instead of the typical tab layout. This can be used to get more freedom in styling the interface.
;<code>&lt;tab&gt;</code>
*<code>inline</code> - If this attribute is set, the tab's contents can be placed within text without interrupting the flow of the text. The difference between this and the default state of <code>display:inline-block</code> is that with <code>inline-block</code>, the tab's contents are forced to a new line when placed at the end of a new line, when not the whole of the tab's contents fit on the same line. <code>inline</code> tabs however will use up any space that's left at the end of the line, and fit in with the normal flow of the text just like normal text.
*<code>block</code> - Converts the tab's contents to a block element. This can be used to assure the tab's contents will be displayed as a block instead of an inline-block, in cases where the tab's contents should not be placed within a line of text. When both the <code>block</code> and <code>inline</code> attributes are available, the <code>inline</code> attribute will be ignored.
*Name attributes:
**<code>index</code> - This will determine the index of the tab. This only works if the entered index is already the index of a defined tab. Otherwise, this attribute is ignored. If no valid index or matching name attributes are defined, the index is automatically set to be the next in the list of tabs.
**<code>name</code> - This attribute is used to define the text the label shows for the tab. If the entered name already exists within the tab, the contents of the <code>&lt;tab&gt;</code> tag are automatically assigned to the existing tab. This also means no two tabs can have an identical label. This attribute will be ignored if the <code>index</code> attribute already refers to an existing tab. Whitespace is automatically removed from the start and end of this attribute's value.
;Both
*Default HTML attributes:
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#title <code>title</code>] - Determines the tooltip shown when hovering over the box.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#style <code>style</code>] - Use this attribute to define any styles for the box. This can also affect the box's label.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#class <code>class</code>] - Adds classes to the box.
**[https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#id <code>id</code>] - Adds an id to the box. This id must be unique on the page, as with any id.

===== Self-closing tabs =====

Self-closing tabs can be used to define a list of tabs at the top of the tab menu, for later use via the <code>index</code> attribute. Self-closing tabs only have an effect when a name is defined, and no (valid) index is defined. The syntax for self-closing tabs is <code>&lt;tab name="name" /&gt;</code>

===== Parser function =====

As an alternative for the tab tag, the <code><nowiki>{{#tab:}}</nowiki></code> parser function can also be used to simplify the syntax for tabs. The syntax for this parser function allows the following syntaxes:

<pre>
{{#tab:name/index 1, name/index 2, etc|content 1|content 2|etc}}
{{#tab:|content 1|content 2|etc}}
{{#tab:name/index 1, , name/index 3, name/index 4|content 1|content 2| |content 4}}
{{#tab:name 1, name 2, name 3...}}
{{#tab:name/index 1, etc|content 1|$1}}
</pre>
These have the following effects:
#Each of the defined names will be set as <code>name</code> or <code>index</code> attributes, respectively.
#*All values that are numbers only will be automatically recognised as indices. For indices, surrounding whitespace is allowed, but internal whitespace or any non-number characters such as decimal points aren't.
#*If these condtions are not met, the entered value is interpreted as a name.
#*If the entered value contains only whitespace or is left empty, the index of that tab within the parser function is assumed.
#No indices or names are defined here, so the indices of the tabs within the parser functions are automatically assigned as index.
#The second tab will automatcally get <code>index="1"</code>, and the third tab will have no content:
#*If the third tab has a name defined in the list of names, then a [[#Self-closing tabs|self-closing tag.
#*If the third tab has an index defined, this tab is skipped, and no output is generated for this tab.
#This will define three tabs, "name 1", "name 2" and "name 3" using the [[#Self-closing tabs|self-closing syntax]].
#When the content of a tab is <code>$n</code> (where <code>n</code> is the place of the tab in the parser function), the contents of that tab are copied over to the tab that has <code>$n</code> in it. This only works if the tab contains nothing other than <code>$n</code>, and the parser function's <code>n</code>th parameter is defined and not empty.

==== Demos ====

===== Naming and reusing tabs, and default text =====

<tabs>
<tab name="First" style="border:1px solid black;">This tab has a defined <code>name</code>. It also has a <code>style</code> attribute set to <code>style="border:1px solid black;"</code>.</tab>
<tab name="Second" style="background:salmon;">This tab also has a defined <code>name</code> attribute, and its <code>style</code> attribute set to <code>style="background:salmon;"</code>.</tab>
<tab>This tab has no attributes defined. Its name is automatically generated based on its position.</tab>
<tab index="1">This is a seperate tab. It has a defined <code>index</code> attribute with value "1". This makes it also show when the first tab is selected.</tab>
<tab name="Second">This is a seperate tab. It has a defined <code>name</code> attribute, with a value equal to that of the second tab ("Second"). It therefore also shows when the second tab is opened.</tab>
----
This line of text will show for every tab you view. It is not placed within <code>&lt;tab&gt; tags, and can be used as default content for the tab menu.
</tabs>

===== <code>block</code> and <code>inline</code> tabs =====

<tabs>
<tab name="Default 1" style="background:lightgreen;">First tab.</tab>
<tab name="Default 2" style="background:lightgreen;">Second tab.</tab>
<tab name="Inline" style="background:salmon;">Third tab.</tab>
<tab name="Block" style="background:royalblue;">Fourth tab.</tab>
<tab index="1">This is a seperate tab. It demonstrates what happens if a tab has no <code>inline</code> or <code>block</code> attributes defined. If the tab contains a lot of text, it will automatically be forced to a new line, despite extra space being available at the end of the previous line.</tab>
<tab index="2">This seperate tab isn't forced to a new line, since it's short enough.</tab>
<tab index="3" inline>This is a seperate tab that has an <code>inline</code> attribute defined. It will fit in with the text as normal text would, and it fills up any space that is left available after the previous line. This makes tabs with <code>inline</code> attributes a bit better at fitting in with the flow of text.</tab>
<tab index="4" block>Despite fitting on the previous line, the <code>block</code> attribute forces this seperate tab to a new line</tab>
</tabs>

===== Inline switching parts =====

This tab menu uses the regular syntax using the <code>&lt;tab&gt;</code> tag.
<tabs>
This line of text contains <tab name="Exaggerating">over 9000</tab><tab name="Truth">a couple of</tab> switching parts. The <tab index="1">biggest by far</tab><tab index="2">main</tab> part of this tab's contents is placed outside any <tab index="1">awesome</tab> <code>&lt;tab&gt;</code> tags.

The switching <tab index="1">epicness</tab><tab index="2">parts</tab> are made by putting <code>&lt;tab&gt;</code> tags within the flow of the text.
</tabs>
----
This tab menu looks exactly the same, but uses the parser function <code><nowiki>{{#tab:name1, name2|content1|content2}}</nowiki></code> or <code><nowiki>{{#tab:index1, index2|content1|content2}}</nowiki></code>. This makes the code a bit shorter.

<tabs>
This line of text contains {{#tab:Exagerrating,Truth|over 9000|a couple of}} switching parts. The {{#tab:|very biggest|main}} part of this tab's contents is placed outside any {{#tab:|awesome}} <code>&lt;tab&gt;</code> tags.

The switching {{#tab:|epicness|parts}} are made by putting <code>&lt;tab&gt;</code> tags within the flow of the text.
</tabs>

===== Predefining tabs and reference syntax =====

Tabs can be predefined via either [[#Self-closing tabs|self-closing tabs]] or the [[#Parser function|parser function]]. This tab menu's third tab also uses the reference syntax for the parser function.

<tabs>
<tab name="First"/>{{#tab:Second,Third}}
The {{#tab:|first|second|third}} tab is predefined via ''{{#tab:|a self-closing <code>&lt;tab /&gt;</code> tag|the parser-function syntax|$2}}''.

<tab index="3">The italic text in the above line is defined via a <code>$2</code> reference. This automatically inserts the contents for the second value entered into the third tab too.
</tab>
</tabs>

=== Nested combinations ===

In some cases, it is possible to put multiple of these boxes inside each other. For this to work however, the <code>#tag:tabs</code>, <code>#tag:tab</code> or <code>#tab:</code> parser functions will have to be used whenever two of the same tags are used anywhere within each other. This is required because otherwise the wikicode parser will recognise the closing tag for the nested tag as the closing tag for the outer tag, and skip the rest of the content, which could cause problems.

For the <code>#tag:</code> parser function, even boolean attributes (such as <code>dropdown</code> or <code>inline</code>) need to have a value defined for them, otherwise they are not recognised as attributes. For example, <code><nowiki>{{#tag:tab|Dropdown contents|dropdown}}</nowiki></code> will not work (it will show a toggle box instead of a dropdown), while <code><nowiki>{{#tag:tab|Dropdown contents|dropdown=true}}</nowiki></code> will show a dropdown box.

All combinations of nesting multiple tags will work, except for nesting ''any'' tab menus inside other tab menus.

==== Nested tab menus ====

===== Inside toggle boxes =====

<tab>
This tab contains a tab menu:

<tabs>
{{#tab:First, Second|These tabs use the <code>#tab:</code> parser function to create the nested tabs.|Placing <code>&lt;tab&gt;</code> tags inside another <code>&lt;tab&gt;</code> tag will cause the parser to recognise the inner closing tag as the closing tag for the outer tag, which messes it up.}}
</tabs>
</tab>

===== Inside dropdowns =====

<tab dropdown>
And here is another tab menu:
<tabs>
{{#tag:tab|These tabs are generated via the <code>#tag:tab</code> parser function|name="First"}}
{{#tag:tab|This is required, for the same reason as explained in the Second tab in the toggle box example above.|name="Second"}}
</tabs>
</tab>

==== Nested toggle/dropdown boxes ====

===== Toggle boxes in toggle boxes =====
<tab>
This toggle box has another toggle box nested inside it.

{{#tag:tab|This tab is generated by the <code>#tag:tab</code> parser function.|openname=Show|closename="Hide"}}
</tab>

===== Toggle boxes in dropdowns =====
{{#tag:tab|
It is also possible to use the <code>#tag:tab</code> parser function for the outer tab.
<tab collapsed>
This inner toggle box is made via the <code>&lt;tag&gt;</code> syntax.
</tab>
|dropdown=true}}

===== Toggle boxes and dropdowns in tab boxes =====
<tabs>
<tab name="Toggle box">
This first tab has a toggle box nested inside it
{{#tab:Toggle|This toggle box is made via the <code>#tab:</code> parser function.}}
</tab>
<tab name="Dropdown">
This second tab has a dropdown nested inside it
{{#tag:tab|This dropdown is created via the <code>#tag:tab</code> parser function, since it's not possible to define attributes such as <code>dropdown</code> via the <code>#tab:</code> parser function.|dropdown=true}}
</tab>
</tabs>

===== Toggle boxes in dropdowns =====

<tab dropdown name="nested toggle boxes" style="width:250px">
This dropdown has a nested toggle box that has <code>inline</code> and <code>collapsed</code> attributes filled in: {{#tag:tab|You can do the same things with nested boxes as you'd normally do outside other tags.|inline=true|collapsed=true}}
</tab>

===== Dropdowns in dropdowns =====

<tab dropdown name="nested dropdowns">
*It is even possible to have a dropdown inside a list item in another dropdown box
*{{#tag:tab|This a dropdown inside a list in the outer dropdown menu|dropdown=true}}
*And it is even possible to have a dropdown inside sub-menus in the dropdown...
**{{#tag:tab|It also works normally in sub-menus|dropdown=true}}
Or if you want, you can place it outside lists too.
{{#tag:tab|Here's a dropdown inside a dropdown, but not in any list|dropdown=true}}
</tab>
