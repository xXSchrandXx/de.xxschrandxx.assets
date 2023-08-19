<section>
	<section class="section general">
		<dl>
			<dt>{lang}wcf.page.asset.overview.id{/lang}</dt>
			<dd>
				{if ASSETS_LEGACYID_ENABLED}
					{$object->getLegacyID()}
				{else}
					{#$object->getObjectID()}
				{/if}
			</dd>
		</dl>
		<dl>
			<dt>{lang}wcf.page.asset.overview.amount{/lang}</dt>
			<dd>{#$object->getAmount()}</dd>
		</dl>
		<dl>
			<dt>{lang}wcf.page.asset.overview.category{/lang}</dt>
			<dd>{$object->getCategory()->getTitle()}</dd>
		</dl>
		<dl>
			<dt>{lang}wcf.page.asset.overview.location{/lang}</dt>
			<dd>{$object->getLocation()->getTitle()}</dd>
		</dl>
		<dl>
			<dt>{lang}wcf.page.asset.overview.created{/lang}<dt>
			<dd>{time time=$object->getCreatedTimestamp()|}</dd>
		</dl>
	</section>

	<section class="section description">
		<h2 class="messageSectionTitle">{lang}wcf.page.asset.overview.description{/lang}</h2>
		{hascontent}
			<section class="htmlContent" itemprop="description">
				{content}
					{@$object->getDescription()}
				{/content}
			</section>
		{hascontentelse}
			<p class="info">{lang}wcf.global.noDeclaration{/lang}</p>
		{/hascontent}
	</section>

	{include file='attachments' attachmentList=$object->getReadAttachmentList() objectID=$object->getObjectID()}
</section>