{hascontent}
	<section>
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnEditID">{lang}wcf.edit.version{/lang}</th>
					<th class="columnText columnUser">{lang}wcf.user.username{/lang}</th>
					<th class="columnText columnEditAction">{lang}wcf.page.asset.history.action{/lang}</th>
					<th class="columnText columnEditReason">{lang}wcf.edit.reason{/lang}</th>
					<th class="columnDate columnTime">{lang}wcf.edit.time{/lang}</th>

					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{content}
					{foreach from=$modificationLogs item=edit name=edit}
						<tr class="jsEditRow">
							<td class="columnID">{#($tpl[foreach][edit][total] - $tpl[foreach][edit][iteration] + 1)}</td>
							<td class="columnText columnUser"><a href="{link controller='User' id=$edit->userID title=$edit->username}{/link}">{$edit->username}</a></td>
							<td class="columnText columnEditAction">{lang}wcf.asset.action.{$edit->action}{/lang}</td>
							<td class="columnText columnEditReason">{$edit->reason}</td>
							<td class="columnDate columnTime">{@$edit->time|time}</td>

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