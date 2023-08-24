{capture assign='__contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				{lang}wcf.page.assetList.title{/lang}
			</h1>
			{hascontent}
				<h2 class="contentSubTitle">
					<ul>
						{content}
							{if $categoryName|isset}
								<li>{lang}wcf.page.assetList.subtitle.category{/lang}</li>
							{/if}
							{if $locationName|isset}
								<li>{lang}wcf.page.assetList.subtitle.location{/lang}</li>
							{/if}
						{/content}
					</ul>
				</h2>
			{/hascontent}
		</div>

		<nav class="contentHeaderNavigation">
			<ul>
				<li>
					<a href="{link controller='AssetAdd' application='assets'}{/link}" class="button">
						<fa-icon size="16" name="plus"></fa-icon>
						<span>{lang}wcf.form.asset.title.add{/lang}</span>
					</a>
				</li>

				{event name='contentHeaderNavigation'}
			</ul>
		</nav>
	</header>
{/capture}

{include file='header' contentHeader=$__contentHeader}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Controller/Clipboard'], (ControllerClipboard) => {
		ControllerClipboard.setup({
			pageClassName: 'assets\\page\\AssetListPage',
			hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
		});
	});
</script>

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="AssetList" application="assets" link="categoryID=$categoryID&locationID=$locationID&trash=$trash&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		{/content}
	</div>
{/hascontent}

{hascontent}
	<div class="section sectionContainerList">
		<table data-type="de.xxschrandxx.assets.asset" class="table jsClipboardContainer jsObjectActionContainer" data-object-action-class-name="assets\data\asset\AssetAction">
			<thead>
				<tr>
					<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll"></label></th>
					{if ASSETS_LEGACYID_ENABLED}
						<th class="columnID{if $sortField == 'legacyID'} active {@$sortOrder}{/if}">
							<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=legacyID&sortOrder={if $sortField == 'assetID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
								{lang}wcf.global.objectID{/lang}
							</a>
						</th>
					{else}
						<th class="columnID{if $sortField == 'assetID'} active {@$sortOrder}{/if}">
							<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=assetID&sortOrder={if $sortField == 'assetID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
								{lang}wcf.global.objectID{/lang}
							</a>
						</th>
					{/if}
					<th></th>
					<th class="columnTitle{if $sortField == 'title'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.global.title{/lang}
						</a>
					</th>
					<th>{lang}wcf.page.assetList.category{/lang}</th>
					<th class="columnInt{if $sortField == 'amount'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=amount&sortOrder={if $sortField == 'amount' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.amount{/lang}
						</a>
					</th>
					<th>{lang}wcf.page.assetList.location{/lang}</th>
					<th class="columnDate{if $sortField == 'lastTimeModified'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=lastTimeModified&sortOrder={if $sortField == 'lastTimeModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.lastTimeModified{/lang}
						</a>
					</th>
					<th class="columnDate{if $sortField == 'time'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.time{/lang}
						</a>
					</th>

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{content}
					{foreach from=$objects item=object}
						<tr class="jsObjectRow jsClipboardObject jsObjectActionObject{if $object->isTrashed()} trashed{/if}" data-object-id="{@$object->getObjectID()}" data-name="{$object->getTitle()}">
							<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$object->getObjectID()}"></td>
							{if ASSETS_LEGACYID_ENABLED}
								<td class="columnID">{$object->getLegacyID()}</td>
							{else}
								<td class="columnID">{#$object->getObjectID()}</td>
							{/if}
							<td class="columnIcon">
								{if $object->canView()}
									<a href="{link controller='Asset' application='assets' id=$object->getObjectID()}{/link}" title="{lang}wcf.form.asset.view{/lang}" class="jsTooltip">
										<fa-icon size="16" name="eye"></fa-icon>
									</a>
								{/if}
								{if $object->canModify()}
									<a href="{link controller='AssetEdit' application='assets' id=$object->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
										<fa-icon size="16" name="pencil"></fa-icon>
									</a>
								{/if}
								{if $object->canDelete() && $object->isTrashed()}
									{objectAction action="delete" objectTitle=$object->getTitle()}
								{/if}

								{event name='rowButtons'}
							</td>
							<td class="columnTitle">
								{if $object->canView()}
									<a href="{link controller='Asset' application='assets' id=$object->getObjectID()}{/link}">
								{/if}
									{$object->getTitle()}
								{if $object->canView()}
									</a>
								{/if}
							</td>
							<td class="columnText">{$object->getCategory()->getTitle()}</td>
							<td class="columnInt">{$object->getAmount()}</td>
							<td class="columnText">{$object->getLocation()->getTitle()}</td>
							<td class="columnDate">{@$object->getLastTimeModifiedTimestamp()|time}</td>
							<td class="columnDate">{@$object->getCreatedTimestamp()|time}</td>

							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
{hascontentelse}
	{if $assetCategoryNodeTreeIDs|isset && $assetCategoryNodeTreeIDs|empty && $__wcf->getSession()->getPermission('admin.assets.canManageCategories')}
		<p class="warning">{lang}wcf.page.assetList.noCategories{/lang}</p>
	{/if}
	{if $assetLocationNodeTreeIDs|isset && $assetLocationNodeTreeIDs|empty &&  $__wcf->getSession()->getPermission('admin.assets.canManageLocations')}
		<p class="warning">{lang}wcf.page.assetList.noLocations{/lang}</p>
	{/if}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}

{hascontent}
	<div class="paginationButtom">
		{content}
			{@$pagesLinks}
		{/content}
	</div>
{/hascontent}

{include file='footer'}
