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

	require(['Language', 'xXSchrandXx/Assets/Ui/Asset/ListEditor'], function(Language, UiAssetListEditor) {
		Language.addObject({
			'assets.asset.audit': '{jslang}assets.asset.audit{/jslang}',
			'assets.asset.audit.comment.optional': '{jslang}assets.asset.audit.comment.optional{/jslang}',
			'assets.asset.trash': '{jslang}assets.asset.trash{/jslang}',
			'assets.asset.restore': '{jslang}assets.asset.restore{/jslang}'
		});
		new UiAssetListEditor();
	});

	{event name='javascriptInit'}
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
					<th class="columnDate{if $sortField == 'nextAudit'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=nextAudit&sortOrder={if $sortField == 'nextAudit' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.nextAudit{/lang}
						</a>
					</th>
					<th class="columnDate{if $sortField == 'lastAudit'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=lastAudit&sortOrder={if $sortField == 'lastAudit' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.lastAudit{/lang}
						</a>
					</th>
					<th class="columnDate{if $sortField == 'lastModification'} active {@$sortOrder}{/if}">
						<a href="{link controller='AssetList' application="assets"}&categoryID={@$categoryID}&locationID={@$locationID}&trash={@$trash}&pageNo={@$pageNo}&sortField=lastModification&sortOrder={if $sortField == 'lastModification' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
							{lang}wcf.page.assetList.lastModification{/lang}
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
						<tr 
							class="jsAssetRow jsClipboardObject" 
							data-object-id="{@$object->getObjectID()}" 
							data-name="{@$object->getTitle()}" 
							data-trashed="{if $object->isTrashed()}true{else}false{/if}" 
							data-can-audit="{if $object->canAudit()}true{else}false{/if}" 
							data-can-trash="{if $object->canTrash()}true{else}false{/if}" 
							data-can-restore="{if $object->canRestore()}true{else}false{/if}" 
							data-can-delete="{if $object->canDelete()}true{else}false{/if}" 
							data-can-modify="{if $object->canModify()}true{else}false{/if}"
						>
							<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$object->getObjectID()}"></td>
							{if ASSETS_LEGACYID_ENABLED}
								<td class="columnID">{$object->getLegacyID()}</td>
							{else}
								<td class="columnID">{#$object->getObjectID()}</td>
							{/if}
							<td class="columnIcon">
								<div class="dropdown" id="assetListDropdown{@$object->getObjectID()}">
									<a href="#" class="dropdownToggle button small">{icon name='pencil'} <span>{lang}wcf.global.button.edit{/lang}</span></a>

									<ul class="dropdownMenu">
										{event name='dropdownItems'}

										<li>
											<a 
												href="#" 
												class="jsAudit" 
												{if !$object->canAudit() || $object->isTrashed()}hidden{/if}
											>
												{lang}assets.asset.audit{/lang}
											</a>
										</li>
										<li>
											<a 
												href="#" 
												class="jsTrash" 
												{if !$object->canTrash() || $object->isTrashed()}hidden{/if}
											>
												{lang}assets.asset.trash{/lang}
											</a>
										</li>
										<li>
											<a 
												href="#" 
												class="jsRestore" 
												{if !$object->canRestore() || !$object->isTrashed()}hidden{/if}
											>
												{lang}assets.asset.restore{/lang}
											</a>
										</li>
										<li>
											<a 
												href="#" 
												class="jsDelete" 
												data-confirm-message="{lang __encode=true objectTitle=$object->getTitle()}wcf.button.delete.confirmMessage{/lang}" 
												{if !$object->canDelete() || !$object->isTrashed()}hidden{/if}
											>
												{lang}wcf.global.button.delete{/lang}
											</a>
										</li>
										{if $object->canModify()}
											<li class="dropdownDivider"></li>
											<li>
												<a 
													href="{link controller='AssetEdit' application='assets' id=$object->getObjectID()}{/link}" 
													class="jsEditLink"
												>
													{lang}wcf.global.button.edit{/lang}
												</a>
											</li>
										{/if}
									</ul>
								</div>
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
							<td class="columnDate">{time time=$object->getNextAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastModificationDateTime()}</td>
							<td class="columnDate">{time time=$object->getCreatedDateTime()}</td>

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
	<div class="paginationBottom">
		{content}
			{@$pagesLinks}
		{/content}
	</div>
{/hascontent}

{include file='footer'}
