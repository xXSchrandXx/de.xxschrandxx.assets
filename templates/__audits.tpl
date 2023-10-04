{hascontent}
	<section>
		<table class="table">
			<thead>
				<tr>
					<th class="columnText columnUser">{lang}wcf.user.username{/lang}</th>
					<th class="columnText columnEditAction">{lang}assets.page.asset.history.action{/lang}</th>
					<th class="columnText columnEditReason">{lang}assets.edit.comment{/lang}</th>
					<th class="columnDate columnTime">{lang}wcf.edit.time{/lang}</th>

					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{content}
					{foreach from=$auditLogs item=edit name=edit}
						<tr>
							<td class="columnText columnUser"><a href="{link controller='User' id=$edit->userID title=$edit->username}{/link}">{$edit->username}</a></td>
							<td class="columnText columnEditAction">{lang}assets.asset.action.{$edit->action}{/lang}</td>
							<td class="columnText columnEditReason">{$edit->comment}</td>
							<td class="columnDate columnTime">{time time=$edit->time}</td>

							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</section>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}