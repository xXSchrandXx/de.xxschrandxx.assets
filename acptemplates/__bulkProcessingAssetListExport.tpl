{hascontent}
	<div class="section sectionContainerList">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID">
						{lang}wcf.global.objectID{/lang}
					</th>
					<th class="columnTitle">
						{lang}wcf.global.title{/lang}
					</th>
					<th>
						{lang}assets.acp.export.category{/lang}
					</th>
					<th class="columnInt">
						{lang}assets.acp.export.amount{/lang}
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

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody>
				{content}
					{foreach from=$objects item=object}
						<tr>
							{if ASSETS_LEGACYID_ENABLED}
								<td class="columnID">{$object->getLegacyID()}</td>
							{else}
								<td class="columnID">{#$object->getObjectID()}</td>
							{/if}
							<td class="columnTitle">{$object->getTitle()}</td>
							<td class="columnText">{$object->getCategory()->getTitle()}</td>
							<td class="columnInt">{$object->getAmount()}</td>
							<td class="columnText">{$object->getLocation()->getTitle()}</td>
							<td class="columnDate">{time time=$object->getNextAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastAuditDateTime()}</td>
							<td class="columnDate">{time time=$object->getLastModificationDateTime()}</td>
							<td class="columnDate">{time time=$object->getCreatedDateTime()}</td>

							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}