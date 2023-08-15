{capture assign='__contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				{lang}wcf.page.assetList.title{/lang}
			</h1>
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
					{content}
						<li>
							<a href="{link controller='AssetAdd' application='assets'}{/link}" class="button">
								<fa-icon size="16" name="plus"></fa-icon>
								<span>{lang}wcf.form.asset.title.add{/lang}</span>
							</a>
						</li>
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{include file='header' contentHeader=$__contentHeader}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="AssetList" application="assets" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		{/content}
	</div>
{/hascontent}

{hascontent}
	<div class="section sectionContainerList">
		<table class="table jsObjectActionContainer" data-object-action-class-name="assets\data\asset\AssetAction">
			<thead>
				<tr>
					<th></th>
					{if ASSETS_LEGACYID_ENABLED}
						<th class="columnID{if $sortField == 'legacyID'} active {@$sortOrder}{/if}">
							<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=legacyID&sortOrder={if $sortField == 'legacyID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
								{lang}wcf.global.objectID{/lang}
							</a>
						</th>
					{else}
						<th class="columnID{if $sortField == 'assetID'} active {@$sortOrder}{/if}">
							<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=assetID&sortOrder={if $sortField == 'assetID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
								{lang}wcf.global.objectID{/lang}
							</a>
						</th>
					{/if}
					<th class="columnTitle{if $sortField == 'title'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.global.title{/lang}
						</a>
					</th>
					<th>{lang}wcf.page.assetList.category{/lang}</th>
					<th class="columnInt{if $sortField == 'amount'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=amount&sortOrder={if $sortField == 'amount' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.amount{/lang}
						</a>
					</th>
					<th>{lang}wcf.page.assetList.location{/lang}</th>
					<th class="columnDate{if $sortField == 'lastTimeModified'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=lastTimeModified&sortOrder={if $sortField == 'lastTimeModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.lastTimeModified{/lang}
						</a>
					</th>
					<th class="columnDate{if $sortField == 'time'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.time{/lang}
						</a>
					</th>

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{content}
					{foreach from=$objects item=object}
						<tr class="jsObjectRow jsObjectActionObject{if $object->isTrashed()} trashed{/if}" data-object-id="{@$object->getObjectID()}" data-name="{$object->getTitle()}">
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
							{if ASSETS_LEGACYID_ENABLED}
								<td class="columnID">{$object->getLegacyID()}</td>
							{else}
								<td class="columnID">{#$object->getObjectID()}</td>
							{/if}
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
