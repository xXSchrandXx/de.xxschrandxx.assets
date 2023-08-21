<dl>
	<dt><label for="assetCategoryID">{lang}wcf.search.searchInCategories{/lang}</label></dt>
	<dd>
		<select name="assetCategoryID" id="assetCategoryID">
			<option value="">{lang}wcf.global.noSelection{/lang}</option>
			{foreach from=$assetCategoryList item=category}
				<option value="{$category->categoryID}">{if $category->getDepth() > 1}{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:-1+$category->getDepth()}{/if}{$category->getTitle()}</option>
			{/foreach}
		</select>
	</dd>
</dl>

<dl>
	<dt><label for="assetLocationID">{lang}wcf.search.searchInLocations{/lang}</label></dt>
	<dd>
		<select name="assetLocationID" id="assetLocationID">
			<option value="">{lang}wcf.global.noSelection{/lang}</option>
			{foreach from=$assetLocationList item=location}
				<option value="{$location->locationID}">{if $location->getDepth() > 1}{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:-1+$location->getDepth()}{/if}{$location->getTitle()}</option>
			{/foreach}
		</select>
	</dd>
</dl>

{event name='fields'}