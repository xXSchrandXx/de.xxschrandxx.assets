{capture assign='__contentHeader'}
	<header
		class="contentHeader"
		data-object-id="{$object->getObjectID()}"
	>
		<div class="contentHeaderTitle {if $object->isTrashed()} trashed{/if}">
			<h1 class="contentTitle">
				{$object->getTitle()}
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
	{if $object->canDelete()}
		<button
			type="button"
			class="contentInteractionButton button small jsButtonAssetRestore"
			{if !$object->isTrashed()} style="display: none"{/if}
		>
			<fa-icon size="16" name="rotate-left"></fa-icon>
			<span>{lang}wcf.global.button.restore{/lang}</span>
		</button>
		<button
			type="button"
			class="contentInteractionButton button small jsButtonAssetDelete"
			{if !$object->isTrashed()} style="display: none"{/if}
		>
			<fa-icon size="16" name="xmark"></fa-icon>
			<span>{lang}wcf.global.button.delete{/lang}</span>
		</button>
	{/if}
	{if $object->canTrash()}
		<button
			type="button"
			class="contentInteractionButton button small jsButtonAssetTrash"
			{if $object->isTrashed()} style="display: none"{/if}
		>
			<fa-icon size="16" name="trash-can"></fa-icon>
			<span>{lang}wcf.global.button.trash{/lang}</span>
		</button>
	{/if}
{/capture}

{include file='header' contentHeader=$__contentHeader contentInteraction=$contentInteractionButtons}

<div class="section tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="#overview">{lang}wcf.page.asset.overview{/lang}</a></li>
			<li><a href="#comments">{lang}wcf.global.comments{/lang} <span class="badge">{#$object->getCommentCount()}</span></a></li>
			<li><a href="#history">{lang}wcf.page.asset.history{/lang} <span class="badge">{#$modificationLogs|count}</span></a></li>

			{event name='tabMenuTabs'}
		</ul>
	</nav>
	
	<div id="overview" class="tabMenuContent">
		{include file='__overview' application='assets'}
	</div>
	<div id="comments" class="tabMenuContent">
		{include file='comments'}
	</div>
	<div id="history" class="tabMenuContent">
		{include file='__history' application='assets'}
	</div>

	{event name='tabMenuContents'}
</div>

{include file='footer'}

<script data-relocate="true">
	require(['Language', 'xXSchrandXx/Assets/Ui/Asset/Editor'], function(Language, Editor) {
		Language.addObject({
			'wcf.page.asset.button.delete.confirmMessage': '{jslang objectTitle=$object->getTitle()}wcf.button.delete.confirmMessage{/jslang}',
			'wcf.page.asset.button.restore.confirmMessage': '{jslang}wcf.page.asset.button.restore.confirmMessage{/jslang}',
			'wcf.page.asset.button.trash.confirmMessage': '{jslang}wcf.page.asset.button.trash.confirmMessage{/jslang}',
			'wcf.page.asset.button.delete.redirect': '{link controller="AssetList" application="assets"}{/link}'
		});
    	Editor.init();
	});
</script>