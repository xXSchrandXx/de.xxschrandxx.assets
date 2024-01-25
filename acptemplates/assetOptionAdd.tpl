{include file='header' pageTitle='assets.acp.asset.option.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}assets.acp.asset.option.{@$action}{/lang}</h1>
	</div>
</header>

{include file='formNotice'}

<form method="post" action="{if $action === 'add'}{link controller='AssetOptionAdd' application='assets'}{/link}{else}{link controller='AssetOptionEdit'  application='assets' id=$optionID}{/link}{/if}">
	{include file='customOptionAdd' application='wcf'}
	
	{event name='sections'}
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{csrfToken}
	</div>
</form>

{include file='footer'}
