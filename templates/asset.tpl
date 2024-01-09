{capture assign='__contentHeader'}
	<header
		class="contentHeader jsAsset"
		data-object-id="{$object->getObjectID()}"
		data-title="{$object->getTitle()}" 
		data-trashed="{if $object->isTrashed()}true{else}false{/if}" 
		data-can-audit="{if $object->canAudit()}true{else}false{/if}" 
		data-can-trash="{if $object->canTrash()}true{else}false{/if}" 
		data-can-restore="{if $object->canRestore()}true{else}false{/if}" 
		data-can-delete="{if $object->canDelete()}true{else}false{/if}" 
		data-can-modify="{if $object->canModify()}true{else}false{/if}"
		data-list-url="{link controller='AssetList' application='assets'}{/link}"
		{event name='jsAssetDataset'}
	>
		<div class="contentHeaderTitle{if $object->isTrashed()} trashed{/if}">
			<h1 class="contentTitle">
				{if $highlightTitle}
					<span class="highlight">
				{/if}
				{$object->getTitle()}
				{if $highlightTitle}
					</span>
				{/if}
			</h1>
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
					{content}
						{if $object->canModify()}
							<li>
								<a class="button" href="{link controller='AssetEdit' application='assets' id=$object->getObjectID()}{/link}">
									<fa-icon size="16" name="pencil"></fa-icon>
									<span>{lang}wcf.global.button.edit{/lang}</span>
								</a>
							</li>
						{/if}

						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{capture assign='contentInteractionButtons'}
	{if $__wcf->session->getPermission('mod.assets.canModify')}
		<button 
			type="button" 
			class="button small contentInteractionButton jsAudit" 
			{if !$object->canAudit() || $object->isTrashed()}hidden{/if}
		>
			{icon name='rotate-left'}
			<span>{lang}assets.asset.audit{/lang}</span>
		</button>
		<button 
			type="button" 
			class="button small contentInteractionButton jsTrash" 
			{if !$object->canTrash() || $object->isTrashed()}hidden{/if}
		>
			{icon name='trash-can'}
			{lang}assets.asset.trash{/lang}
		</button>
		<button 
			type="button" 
			class="button small contentInteractionButton jsRestore" 
			{if !$object->canRestore() || !$object->isTrashed()}hidden{/if}
		>
			{icon name='trash-arrow-up'}
			{lang}assets.asset.restore{/lang}
		</button>
		<button 
			type="button" 
			class="button small contentInteractionButton jsDelete" 
			{if !$object->canDelete() || !$object->isTrashed()}hidden{/if}
		>
			{icon name='x'}
			{lang}wcf.global.button.delete{/lang}
		</button>
	{/if}

	{event name='contentInteractionButtons'}
{/capture}

{include file='header' contentHeader=$__contentHeader contentInteraction=$contentInteractionButtons}

<div 
	class="section tabMenuContainer"
>
	<nav class="tabMenu">
		<ul>
			<li><a href="#overview">{lang}assets.page.asset.overview{/lang}</a></li>
			{if $__wcf->session->getPermission('user.assets.canViewComments')}
				<li><a href="#comments">{lang}wcf.global.comments{/lang} <span class="badge">{#$object->getCommentCount()}</span></a></li>
			{/if}
			{if $__wcf->session->getPermission('user.assets.canViewAuditsTab')}
				<li><a href="#audits">{lang}assets.page.asset.audits{/lang} <span class="badge">{#$auditLogs|count}</span></a></li>
			{/if}
			{if $__wcf->session->getPermission('user.assets.canViewHistoryTab')}
			<li><a href="#history">{lang}assets.page.asset.history{/lang} <span class="badge">{#$modificationLogs|count}</span></a></li>
			{/if}

			{event name='tabMenuTabs'}
		</ul>
	</nav>
	
	<div id="overview" class="tabMenuContent">
		{include file='__overview' application='assets'}
	</div>
	{if $__wcf->session->getPermission('user.assets.canViewComments')}
		<div id="comments" class="tabMenuContent">
			{if $object->getLastCommentDateTime() !== null}
				{assign var=lastCommentTime value=$object->getLastCommentDateTime()->format('U')}
			{/if}
			{include file='comments'}
		</div>
	{/if}
	{if $__wcf->session->getPermission('user.assets.canViewAuditsTab')}
		<div id="audits" class="tabMenuContent">
			{include file='__audits' application='assets'}
		</div>
	{/if}
	{if $__wcf->session->getPermission('user.assets.canViewHistoryTab')}
		<div id="history" class="tabMenuContent">
			{include file='__history' application='assets'}
		</div>
	{/if}

	{event name='tabMenuContents'}
</div>

{if $__wcf->session->getPermission('mod.assets.canModify')}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Language', 'xXSchrandXx/Assets/Ui/Asset/Editor'], function(Language, UiAssetEditor) {
			Language.registerPhrase('wcf.dialog.confirmation.audit', '{jslang __literal=true}wcf.dialog.confirmation.audit{/jslang}');
			new UiAssetEditor();
		});
	</script>
{/if}

{include file='footer'}
