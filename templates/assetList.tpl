{capture assign='__contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				{lang}assets.page.assetList.title{/lang}
			</h1>
			{hascontent}
				<h2 class="contentSubTitle">
					<ul>
						{content}
							{if $categoryName|isset}
								<li>{lang}assets.page.assetList.subtitle.category{/lang}</li>
							{/if}
							{if $locationName|isset}
								<li>{lang}assets.page.assetList.subtitle.location{/lang}</li>
							{/if}
						{/content}
					</ul>
				</h2>
			{/hascontent}
		</div>

		<nav class="contentHeaderNavigation">
			<ul>
				{if $__wcf->session->getPermission('mod.assets.canAdd')}
					<li>
						<a href="{link controller='AssetAdd' application='assets'}{/link}" class="button">
							<fa-icon size="16" name="plus"></fa-icon>
							<span>{lang}assets.form.asset.title.add{/lang}</span>
						</a>
					</li>
				{/if}

				{event name='contentHeaderNavigation'}
			</ul>
		</nav>
	</header>
{/capture}

{include file='header' contentHeader=$__contentHeader}

{hascontent}
	<div class="paginationTop">
		{content}
			<woltlab-core-pagination 
				page="{$pageNo}" 
				count="{$pages}" 
				url="{$canonicalURL}"
			></woltlab-core-pagination>
		{/content}
	</div>
{/hascontent}

{hascontent}
	<div class="section tabularBox">
		<table data-type="de.xxschrandxx.assets.asset" class="table jsClipboardContainer jsObjectActionContainer" data-object-action-class-name="assets\data\asset\AssetAction">
			<thead>
				<tr>
					{if $__wcf->session->getPermission('mod.assets.canModify')}
						<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll"></label></th>
					{/if}
					{if ASSETS_LEGACYID_ENABLED}
						<th class="columnID{if $sortField == 'legacyID'} active {$sortOrder}{/if}">
							{lang}wcf.global.objectID{/lang}
						</th>
					{else}
						<th class="columnID{if $sortField == 'assetID'} active {$sortOrder}{/if}">
							{lang}wcf.global.objectID{/lang}
						</th>
					{/if}
					{if $__wcf->session->getPermission('mod.assets.canModify')}
						<th></th>
					{/if}
					<th class="columnTitle{if $sortField == 'title'} active {$sortOrder}{/if}">
						{lang}wcf.global.title{/lang}
					</th>
					<th>{lang}assets.page.assetList.category{/lang}</th>
					<th class="columnInt{if $sortField == 'amount'} active {$sortOrder}{/if}">
						{lang}assets.page.assetList.amount{/lang}
					</th>
					<th>{lang}assets.page.assetList.location{/lang}</th>
					<th class="columnDate{if $sortField == 'nextAudit'} active {$sortOrder}{/if}">
						{lang}assets.page.assetList.nextAudit{/lang}
					</th>
					<th class="columnDate{if $sortField == 'lastAudit'} active {$sortOrder}{/if}">
						{lang}assets.page.assetList.lastAudit{/lang}
					</th>
					<th class="columnDate{if $sortField == 'lastModification'} active {$sortOrder}{/if}">
						{lang}assets.page.assetList.lastModification{/lang}
					</th>
					<th class="columnDate{if $sortField == 'time'} active {$sortOrder}{/if}">
						{lang}assets.page.assetList.time{/lang}
					</th>
					{foreach from=$options item=option key=optionName}
						<th class="column{$option->optionType|ucfirst}">
							{$option->getTitle()}
						</th>
					{/foreach}

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{content}
					{foreach from=$objects item=object}
						<tr 
							class="jsAssetRow jsClipboardObject" 
							data-object-id="{$object->getObjectID()}" 
							data-title="{$object->getTitle()}" 
							data-trashed="{if $object->isTrashed()}true{else}false{/if}" 
							data-can-audit="{if $object->canAudit()}true{else}false{/if}" 
							data-can-trash="{if $object->canTrash()}true{else}false{/if}" 
							data-can-restore="{if $object->canRestore()}true{else}false{/if}" 
							data-can-delete="{if $object->canDelete()}true{else}false{/if}" 
							data-can-modify="{if $object->canModify()}true{else}false{/if}"
							{event name='jsAssetRowDataset'}
						>
							{if $__wcf->session->getPermission('mod.assets.canModify')}
								<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{$object->getObjectID()}"></td>
							{/if}
							{if ASSETS_LEGACYID_ENABLED}
								<td class="columnID">{$object->getLegacyID()}</td>
							{else}
								<td class="columnID">{#$object->getObjectID()}</td>
							{/if}
							{if $__wcf->session->getPermission('mod.assets.canModify')}
								<td class="columnIcon">
									<div class="dropdown" id="assetListDropdown{$object->getObjectID()}">
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
							<td class="columnDate">{time time=$object->getNextAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastModificationDateTime()}</td>
							<td class="columnDate">{time time=$object->getCreatedDateTime()}</td>
							{foreach from=$options item=option}
								<td class="column{$option->optionType|ucfirst}">{$object->getFormattedOptionValue($option, true)}</td>
							{/foreach}

							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
{hascontentelse}
	{if $assetCategoryNodeTreeIDs|isset && $assetCategoryNodeTreeIDs|empty && $__wcf->getSession()->getPermission('admin.assets.canManageCategories')}
		<p class="warning">{lang}assets.page.assetList.noCategories{/lang}</p>
	{/if}
	{if $assetLocationNodeTreeIDs|isset && $assetLocationNodeTreeIDs|empty &&  $__wcf->getSession()->getPermission('admin.assets.canManageLocations')}
		<p class="warning">{lang}assets.page.assetList.noLocations{/lang}</p>
	{/if}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}

{hascontent}
	<div class="paginationBottom">
		{content}
			<woltlab-core-pagination 
				page="{$pageNo}" 
				count="{$pages}" 
				url="{$canonicalURL}"
			></woltlab-core-pagination>
		{/content}
	</div>
{/hascontent}

{if $__wcf->session->getPermission('mod.assets.canModify')}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Language', 'WoltLabSuite/Core/Controller/Clipboard', 'xXSchrandXx/Assets/Ui/Asset/ClipboardListener', 'xXSchrandXx/Assets/Ui/Asset/ListEditor'], (Language, ControllerClipboard, ClipboardListener, UiAssetListEditor) => {
			Language.registerPhrase('wcf.dialog.confirmation.audit', '{jslang __literal=true}wcf.dialog.confirmation.audit{/jslang}');
			ControllerClipboard.setup({
				pageClassName: 'assets\\page\\AssetListPage',
				hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
			});
			new ClipboardListener();
			new UiAssetListEditor();
		});
	</script>
{/if}

{include file='footer'}
