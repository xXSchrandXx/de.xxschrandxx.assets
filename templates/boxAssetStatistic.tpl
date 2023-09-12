{if $stats|isset}
	<ul class="inlineList dotSeparated">
		<li>
			{#$stats['categoryCount']} {lang categoryCount=$stats['categoryCount']}wcf.box.de.xxschrandxx.assets.AssetStatistics.categories{/lang}
		</li>
		<li>
			{#$stats['locationCount']} {lang locationCount=$stats['locationCount']}wcf.box.de.xxschrandxx.assets.AssetStatistics.locations{/lang}
		</li>
		<li>
			{#$stats['assetCount']} {lang assetCount=$stats['assetCount']}wcf.box.de.xxschrandxx.assets.AssetStatistics.assets{/lang} ({#$stats['assetsPerDay']} {lang assetCount=$stats['assetsPerDay']}wcf.box.de.xxschrandxx.assets.AssetStatistics.assets{/lang} {lang}wcf.box.de.xxschrandxx.assets.AssetStatistics.perDay{/lang})
		</li>
		<li>
			{#$stats['auditCount']} {lang auditCount=$stats['auditCount']}wcf.box.de.xxschrandxx.assets.AssetStatistics.audits{/lang} ({#$stats['auditsPerDay']} {lang auditCount=$stats['auditsPerDay']}wcf.box.de.xxschrandxx.assets.AssetStatistics.audits{/lang} {lang}wcf.box.de.xxschrandxx.assets.AssetStatistics.perDay{/lang})
		</li>
		<li>
			{#$stats['modificationCount']} {lang modificationCount=$stats['modificationCount']}wcf.box.de.xxschrandxx.assets.AssetStatistics.modifications{/lang} ({#$stats['modificationsPerDay']} {lang modificationCount=$stats['modificationsPerDay']}wcf.box.de.xxschrandxx.assets.AssetStatistics.modifications{/lang} {lang}wcf.box.de.xxschrandxx.assets.AssetStatistics.perDay{/lang})
		</li>

		{event name='assetStatsList'}
	</ul>
{/if}