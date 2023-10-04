{capture assign='__contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				{lang}assets.form.asset.title.{$action}{/lang}
			</h1>
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
					{content}
						{if $action == 'edit'}
							<li>
								<a href="{$formObject->getLink()}" class="button">
									<fa-icon size="16" name="magnifying-glass"></fa-icon>
									<span>{lang}assets.form.asset.view{/lang}</span>
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

{include file='header' contentHeader=$__contentHeader}

{@$form->getHtml()}

{include file='footer'}
