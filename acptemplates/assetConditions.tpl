{if !$groupedObjectTypes|isset && $conditions|isset}{assign var='groupedObjectTypes' value=$conditions}{/if}

	<div class="section tabMenuContainer">
		<nav class="tabMenu">
			<ul>
				{foreach from=$groupedObjectTypes key='conditionGroup' item='conditionObjectTypes'}
					<li><a href="#asset_{$conditionGroup|rawurlencode}">{lang}assets.acp.asset.bulkProcessing.conditionGroup.{$conditionGroup}{/lang}</a></li>
				{/foreach}
			</ul>
		</nav>
		
		{foreach from=$groupedObjectTypes key='conditionGroup' item='conditionObjectTypes'}
			<div id="asset_{$conditionGroup}" class="tabMenuContent">
				{if $conditionGroup != 'assetOptions'}
					<section class="section">
						<h2 class="sectionTitle">{lang}assets.acp.asset.bulkProcessing.conditionGroup.{$conditionGroup}{/lang}</h2>
				{/if}
				
				{foreach from=$conditionObjectTypes item='condition'}
					{@$condition->getProcessor()->getHtml()}
				{/foreach}
				
				{if $conditionGroup != 'assetOptions'}
					</section>
				{/if}
			</div>
		{/foreach}
	</div>
	