<div class="row rowColGap formGrid">
	<section class="col-xs-12 col-md-4" id="category">
		<h2 class="messageSectionTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.category{/lang}</h2>
		{include file='boxCategories' categoryList=$categoryCategoryList activeCategory=$categoryActiveCategory resetFilterLink=$categoryResetFilterLink}
	</section>
	<section class="col-xs-12 col-md-4" id="location">
		<h2 class="messageSectionTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.location{/lang}</h2>
		{include file='boxCategories' categoryList=$locationCategoryList activeCategory=$locationActiveCategory resetFilterLink=$locationResetFilterLink}
	</section>
	{if $canSeeTrashed}
		<section class="col-xs-12 col-md-4" id="trash">
			<h2 class="messageSectionTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.trash{/lang}</h2>
			<ol class="boxMenu">
				<li class="boxMenuItem boxMenuItemDepth1{if $trash|isset && $trash == 0} active{/if}">
					<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash=0{/link}" class="boxMenuLink">
						<span class="boxMenuLinkTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.trash.both{/lang}</span>
					</a>
				</li>
				<li class="boxMenuItem boxMenuItemDepth1{if $trash|isset && $trash == 1} active{/if}">
					<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash=1{/link}" class="boxMenuLink">
						<span class="boxMenuLinkTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.trash.yes{/lang}</span>
					</a>
				</li>
				<li class="boxMenuItem boxMenuItemDepth1{if $trash|isset && $trash == 2} active{/if}">
					<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash=2{/link}" class="boxMenuLink">
						<span class="boxMenuLinkTitle">{lang}wcf.box.de.xxschrandxx.assets.assetListFilter.trash.no{/lang}</span>
					</a>
				</li>
			</ol>
		</section>
	{/if}

	{event name='sections'}
</div>