<section class="section general">
	<img src="{@$object->getQRCode()}" class="AssetQRCode" title="QRCode">
	<dl>
		<dt>{lang}assets.page.asset.overview.id{/lang}</dt>
		<dd>
			{if ASSETS_LEGACYID_ENABLED}
				{$object->getLegacyID()}
			{else}
				{#$object->getObjectID()}
			{/if}
		</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.amount{/lang}</dt>
		<dd>{#$object->getAmount()}</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.category{/lang}</dt>
		<dd>{$object->getCategory()->getTitle()}</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.location{/lang}</dt>
		<dd>{$object->getLocation()->getTitle()}</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.nextAudit{/lang}</dt>
		<dd>{time time=$object->getNextAuditDateTime() type='plainDate'}</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.lastAudit{/lang}</dt>
		<dd>{time time=$object->getLastAuditDateTime()}</dd>
	</dl>
	<dl>
		<dt>{lang}assets.page.asset.overview.created{/lang}<dt>
		<dd>{time time=$object->getCreatedDateTime()}</dd>
	</dl>
</section>
<section class="section description">
	<h2 class="messageSectionTitle">{lang}assets.page.asset.overview.description{/lang}</h2>
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
