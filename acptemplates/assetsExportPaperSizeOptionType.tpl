<select id="{$option->optionName}" name="values[{$option->optionName}]">
	{foreach from=$valideFormats item=valideFormat}
		<option value="{@$valideFormat}" {if $valideFormat == $value} selected{/if}>
			{$valideFormat|ucfirst}
		</option>
	{/foreach}
</select>
