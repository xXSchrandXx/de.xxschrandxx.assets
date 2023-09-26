{capture assign='__contentHeader'}
	<header
		class="contentHeader"
		data-object-id="{$object->getObjectID()}"
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
		data-confirm-message="{lang __encode=true objectTitle=$object->getTitle()}wcf.button.delete.confirmMessage{/lang}" 
		{if !$object->canDelete() || !$object->isTrashed()}hidden{/if}
	>
		{icon name='x'}
		{lang}wcf.global.button.delete{/lang}
	</button>

	{event name='contentInteractionButtons'}
{/capture}

{include file='header' contentHeader=$__contentHeader contentInteraction=$contentInteractionButtons}

<div 
	class="section tabMenuContainer jsAsset"
	data-object-id="{@$object->getObjectID()}" 
	data-name="{@$object->getTitle()}" 
	data-trashed="{if $object->isTrashed()}true{else}false{/if}" 
	data-can-audit="{if $object->canAudit()}true{else}false{/if}" 
	data-can-trash="{if $object->canTrash()}true{else}false{/if}" 
	data-can-restore="{if $object->canRestore()}true{else}false{/if}" 
	data-can-delete="{if $object->canDelete()}true{else}false{/if}" 
	data-can-modify="{if $object->canModify()}true{else}false{/if}"
>
	<nav class="tabMenu">
		<ul>
			<li><a href="#overview">{lang}wcf.page.asset.overview{/lang}</a></li>
			<li><a href="#comments">{lang}wcf.global.comments{/lang} <span class="badge">{#$object->getCommentCount()}</span></a></li>
			<li><a href="#audits">{lang}wcf.page.asset.audits{/lang} <span class="badge">{#$auditLogs|count}</span></a></li>
			<li><a href="#history">{lang}wcf.page.asset.history{/lang} <span class="badge">{#$modificationLogs|count}</span></a></li>

			{event name='tabMenuTabs'}
		</ul>
	</nav>
	
	<div id="overview" class="tabMenuContent">
		{include file='__overview' application='assets'}
	</div>
	<div id="comments" class="tabMenuContent">
		{if $object->getLastCommentDateTime() !== null}
			{assign var=lastCommentTime value=$object->getLastCommentDateTime()->format('U')}
		{/if}
		{include file='comments'}
	</div>
	<div id="audits" class="tabMenuContent">
		{include file='__audits' application='assets'}
	</div>
	<div id="history" class="tabMenuContent">
		{include file='__history' application='assets'}
	</div>

	{event name='tabMenuContents'}
</div>

<script data-relocate="true">
	require(['Language', 'xXSchrandXx/Assets/Ui/Asset/Editor'], function(Language, UiAssetEditor) {
		Language.addObject({
			'assets.asset.audit': '{jslang}assets.asset.audit{/jslang}',
			'assets.asset.audit.comment.optional': '{jslang}assets.asset.audit.comment.optional{/jslang}',
			'assets.asset.trash': '{jslang}assets.asset.trash{/jslang}',
			'assets.asset.restore': '{jslang}assets.asset.restore{/jslang}'
		});
		new UiAssetEditor();
	});

	{event name='javascriptInit'}
</script>

{include file='footer'}
