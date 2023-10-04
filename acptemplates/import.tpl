{include file='header' pageTitle='wcf.acp.menu.link.application.assets.import'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.application.assets.import{/lang}</h1>
	</div>
</header>

{if $importError|isset && $importError}
	<p class="error" role="alert">{lang}{$errorType}{/lang}</p>
{/if}

{@$form->getHtml()}

{if $skipped|isset && !$skipped|empty}
	<div class="section">
		<h2 class="sectionTitle">
			{lang}assets.acp.form.import.skip.header{/lang}
		</h2>
		<table class="table">
			<thead>
				<tr>
					<th class="columnID">
						{lang}assets.acp.form.import.skip.rowID{/lang}
					</th>
					<th class="columnTitle">
						{lang}wcf.global.title{/lang}
					</th>
					<th class="columnText">
						{lang}assets.acp.form.import.skip.reason{/lang}
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$skipped item=item key=key}
					<tr>
						<td class="columnID">
							{#$key}
						</td>
						<td class="columnTitle">
							{$item[$columnTitle]}
						</td>
						<td class="columnText">
							{lang}assets.acp.form.import.skip.error.{$item['reason']}{/lang}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}assets.acp.form.import.skip.noSkip{/lang}</p>
{/if}

<div class="section">
	<h2 class="sectionTitle">
		{lang}assets.acp.form.import.example.header{/lang}
	</h2>
	<table class="table">
		<thead>
			<tr>
				<th class="columnID">
					{lang}wcf.global.objectID{/lang}
				</th>
				<th class="columnTitle">
					{lang}wcf.global.title{/lang}
				</th>
				<th class="columnInt">
					{lang}assets.acp.export.categoryID{/lang}
				</th>
				<th>
					{lang}assets.acp.export.category{/lang}
				</th>
				<th class="columnInt">
					{lang}assets.acp.export.amount{/lang}
				</th>
				<th class="columnInt">
					{lang}assets.acp.export.locationID{/lang}
				</th>
				<th>
					{lang}assets.acp.export.location{/lang}
				</th>
				<th class="columnDate">
					{lang}assets.acp.export.nextAudit{/lang}
				</th>
				<th class="columnDate">
					{lang}assets.acp.export.lastAudit{/lang}
				</th>
				<th class="columnDate">
					{lang}assets.acp.export.lastModification{/lang}
				</th>
				<th class="columnDate">
					{lang}assets.acp.export.time{/lang}
				</th>
				<th>
					{lang}wcf.global.description{/lang}
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				{if ASSETS_LEGACYID_ENABLED}
					<td>{lang}assets.acp.form.import.example.required{/lang}</td>
				{else}
					<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
				{/if}
				<td>{lang}assets.acp.form.import.example.required{/lang}</td>
				<td>{lang}assets.acp.form.import.example.required{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optionalID{/lang}</td>
				<td>{lang}assets.acp.form.import.example.required{/lang}</td>
				<td>{lang}assets.acp.form.import.example.required{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optionalID{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
				<td>{lang}assets.acp.form.import.example.optional{/lang}</td>
			</tr>
		</tbody>
	</table>
</div>

{include file='footer'}
