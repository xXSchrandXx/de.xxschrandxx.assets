<button type="button" class="button small assetsFilterButton jsStaticDialog" data-dialog-id="assetsSortFilter">
	{icon name='gear'} {lang}wcf.global.settings{/lang}
</button>
<div id="assetsSortFilter" class="jsStaticDialogContent" data-title="{lang}wcf.global.settings{/lang}">
	<form method="get">
		<section class="section">
			<dl>
				<dt>{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.sortField{/lang}</dt>
				<dd>{htmlOptions name='sortField' options=$validSortFields selected=$sortField}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.global.sorting{/lang}</dt>
				<dd>{htmlOptions name='sortOrder' options=$validSortOrders selected=$sortOrder}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.items{/lang}</dt>
				<dd>
					<input type="number" name="items" min="5" value="{$items}" required>
				</dd>
			</dl>
			{event name='settings'}
		</section>
		{hascontent}
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
				{content}
					{if $canSeeTrashed}
						<dl>
							<dt>{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.trash{/lang}</dt>
							<dd>{htmlOptions name='trash' options=$trashOptions selected=$trash}</dd>
						</dl>
					{/if}
					{assign var=errorType value=''}
					{include file='customOptionFieldList' application='wcf'}
				{/content}
			</section>
		{/hascontent}
		{event name='dialogSections'}
		<section class="formSubmit">
			<a class="button" href="{$optionsResetFilterLink}">{lang}wcf.global.button.reset{/lang}</a>
			<input type="submit" value="Absenden" accesskey="s"/>
		</section>
	</form>
</div>
<script data-relocate="true">
	require(['EventHandler'], function (EventHandler) {
		var container = elById('assetsSortFilter');
		EventHandler.add('com.woltlab.wcf.dialog', 'openStatic', function (data) {
			if (data.id === 'assetsSortFilter') {
				var isSingleSection = (elBySelAll('form > .section', data.content).length === 1);
				container.classList[isSingleSection ? 'add' : 'remove']('jsAssetsSortFilterSingleSection');
			}
		});

		var inputElements = container.querySelectorAll("input");
		inputElements.forEach(function(input) {
			if (input.hasAttribute("name")) {
				var originalName = input.getAttribute("name");
				var modifiedName = originalName.replace(/values\[(.*?)\]/g, '$1');
				input.setAttribute("name", modifiedName);
			}
		});
	});
</script>
<div class="row rowColGap formGrid">
	<section class="col-xs-12 col-md-6" id="category">
		<h2 class="messageSectionTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.category{/lang}</h2>
		{include file='boxCategories' categoryList=$categoryCategoryList activeCategory=$categoryActiveCategory resetFilterLink=$categoryResetFilterLink}
	</section>
	<section class="col-xs-12 col-md-6" id="location">
		<h2 class="messageSectionTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.location{/lang}</h2>
		{include file='boxCategories' categoryList=$locationCategoryList activeCategory=$locationActiveCategory resetFilterLink=$locationResetFilterLink}
	</section>
	{event name='sections'}
</div>